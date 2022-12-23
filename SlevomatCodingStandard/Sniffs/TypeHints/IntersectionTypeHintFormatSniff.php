<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHint;
use function array_merge;
use function explode;
use function implode;
use function sprintf;
use function substr_count;
use const T_TYPE_INTERSECTION;
use const T_VARIABLE;
use const T_WHITESPACE;

class IntersectionTypeHintFormatSniff implements Sniff
{

	public const CODE_DISALLOWED_WHITESPACE = 'DisallowedWhitespace';
	public const CODE_REQUIRED_WHITESPACE = 'RequiredWhitespace';

	private const YES = 'yes';
	private const NO = 'no';

	/** @var bool|null */
	public $enable = null;

	/** @var string|null */
	public $withSpaces = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return array_merge(
			[T_VARIABLE],
			TokenHelper::$functionTokenCodes
		);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 80100);

		if (!$this->enable) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] === T_VARIABLE) {
			if (!PropertyHelper::isProperty($phpcsFile, $pointer)) {
				return;
			}

			$propertyTypeHint = PropertyHelper::findTypeHint($phpcsFile, $pointer);
			if ($propertyTypeHint !== null) {
				$this->checkTypeHint($phpcsFile, $propertyTypeHint);
			}

			return;
		}

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $pointer);
		if ($returnTypeHint !== null) {
			$this->checkTypeHint($phpcsFile, $returnTypeHint);
		}

		foreach (FunctionHelper::getParametersTypeHints($phpcsFile, $pointer) as $parameterTypeHint) {
			if ($parameterTypeHint !== null) {
				$this->checkTypeHint($phpcsFile, $parameterTypeHint);
			}
		}
	}

	private function checkTypeHint(File $phpcsFile, TypeHint $typeHint): void
	{
		$tokens = $phpcsFile->getTokens();

		$typeHintsCount = substr_count($typeHint->getTypeHint(), '&') + 1;

		if ($typeHintsCount <= 1) {
			return;
		}

		if ($this->withSpaces === self::NO) {
			$whitespacePointer = TokenHelper::findNext(
				$phpcsFile,
				T_WHITESPACE,
				$typeHint->getStartPointer() + 1,
				$typeHint->getEndPointer()
			);
			if ($whitespacePointer !== null) {
				$originalTypeHint = TokenHelper::getContent($phpcsFile, $typeHint->getStartPointer(), $typeHint->getEndPointer());
				$fix = $phpcsFile->addFixableError(
					sprintf('Spaces in type hint "%s" are disallowed.', $originalTypeHint),
					$typeHint->getStartPointer(),
					self::CODE_DISALLOWED_WHITESPACE
				);
				if ($fix) {
					$this->fixTypeHint($phpcsFile, $typeHint, $typeHint->getTypeHint());
				}
			}
		} elseif ($this->withSpaces === self::YES) {
			$error = false;
			foreach (TokenHelper::findNextAll(
				$phpcsFile,
				[T_TYPE_INTERSECTION],
				$typeHint->getStartPointer(),
				$typeHint->getEndPointer()
			) as $intersectionSeparator) {
				if ($tokens[$intersectionSeparator - 1]['content'] !== ' ') {
					$error = true;
					break;
				}
				if ($tokens[$intersectionSeparator + 1]['content'] !== ' ') {
					$error = true;
					break;
				}
			}

			if ($error) {
				$originalTypeHint = TokenHelper::getContent($phpcsFile, $typeHint->getStartPointer(), $typeHint->getEndPointer());
				$fix = $phpcsFile->addFixableError(
					sprintf('One space required before and after each "&" in type hint "%s".', $originalTypeHint),
					$typeHint->getStartPointer(),
					self::CODE_REQUIRED_WHITESPACE
				);
				if ($fix) {
					$fixedTypeHint = implode(' & ', explode('&', $typeHint->getTypeHint()));
					$this->fixTypeHint($phpcsFile, $typeHint, $fixedTypeHint);
				}
			}
		}
	}

	private function fixTypeHint(File $phpcsFile, TypeHint $typeHint, string $fixedTypeHint): void
	{
		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->replaceToken($typeHint->getStartPointer(), $fixedTypeHint);
		FixerHelper::removeBetweenIncluding($phpcsFile, $typeHint->getStartPointer() + 1, $typeHint->getEndPointer());

		$phpcsFile->fixer->endChangeset();
	}

}
