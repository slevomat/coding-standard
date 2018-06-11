<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ReturnTypeHintSpacingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT = 'NoSpaceBetweenColonAndTypeHint';

	public const CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT = 'MultipleSpacesBetweenColonAndTypeHint';

	public const CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL = 'NoSpaceBetweenColonAndNullabilitySymbol';

	public const CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL = 'MultipleSpacesBetweenColonAndNullabilitySymbol';

	public const CODE_WHITESPACE_BEFORE_COLON = 'WhitespaceBeforeColon';

	public const CODE_INCORRECT_SPACES_BEFORE_COLON = 'IncorrectWhitespaceBeforeColon';

	public const CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL = 'WhitespaceAfterNullabilitySymbol';

	/** @var int */
	public $spacesCountBeforeColon = 0;

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_CLOSURE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $functionPointer): void
	{
		$typeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);

		if ($typeHint === null) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$typeHintPointer = $typeHint->getStartPointer();

		/** @var int $colonPointer */
		$colonPointer = TokenHelper::findPreviousExcluding($phpcsFile, array_merge([T_NULLABLE], TokenHelper::$ineffectiveTokenCodes), $typeHintPointer - 1);

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $colonPointer + 1);
		$nullabilitySymbolPointer = $nextPointer !== null && $tokens[$nextPointer]['code'] === T_NULLABLE ? $nextPointer : null;

		if ($nullabilitySymbolPointer === null) {
			if ($tokens[$colonPointer + 1]['code'] !== T_WHITESPACE) {
				$fix = $phpcsFile->addFixableError('There must be exactly one space between return type hint colon and return type hint.', $typeHintPointer, self::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE_HINT);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContent($colonPointer, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			} elseif ($tokens[$colonPointer + 1]['content'] !== ' ') {
				$fix = $phpcsFile->addFixableError('There must be exactly one space between return type hint colon and return type hint.', $typeHintPointer, self::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE_HINT);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($colonPointer + 1, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			}
		} else {
			if ($tokens[$colonPointer + 1]['code'] !== T_WHITESPACE) {
				$fix = $phpcsFile->addFixableError('There must be exactly one space between return type hint colon and return type hint nullability symbol.', $typeHintPointer, self::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContent($colonPointer, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			} elseif ($tokens[$colonPointer + 1]['content'] !== ' ') {
				$fix = $phpcsFile->addFixableError('There must be exactly one space between return type hint colon and return type hint nullability symbol.', $typeHintPointer, self::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($colonPointer + 1, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			}

			if ($nullabilitySymbolPointer + 1 !== $typeHintPointer) {
				$fix = $phpcsFile->addFixableError('There must be no whitespace between return type hint nullability symbol and return type hint.', $typeHintPointer, self::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($nullabilitySymbolPointer + 1, '');
					$phpcsFile->fixer->endChangeset();
				}
			}
		}

		$spacesCountBeforeColon = SniffSettingsHelper::normalizeInteger($this->spacesCountBeforeColon);
		$expectedSpaces = str_repeat(' ', $spacesCountBeforeColon);

		if ($tokens[$colonPointer - 1]['code'] !== T_CLOSE_PARENTHESIS && $tokens[$colonPointer - 1]['content'] !== $expectedSpaces) {
			if ($spacesCountBeforeColon === 0) {
				$fix = $phpcsFile->addFixableError('There must be no whitespace between closing parenthesis and return type colon.', $typeHintPointer, self::CODE_WHITESPACE_BEFORE_COLON);
			} else {
				$fix = $phpcsFile->addFixableError(
					sprintf('There must be exactly %d whitespace%s between closing parenthesis and return type colon.', $spacesCountBeforeColon, $spacesCountBeforeColon !== 1 ? 's' : ''),
					$typeHintPointer,
					self::CODE_INCORRECT_SPACES_BEFORE_COLON
				);
			}
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($colonPointer - 1, $expectedSpaces);
				$phpcsFile->fixer->endChangeset();
			}
		} elseif ($tokens[$colonPointer - 1]['code'] === T_CLOSE_PARENTHESIS && $spacesCountBeforeColon !== 0) {
			$fix = $phpcsFile->addFixableError(
				sprintf('There must be exactly %d whitespace%s between closing parenthesis and return type colon.', $spacesCountBeforeColon, $spacesCountBeforeColon !== 1 ? 's' : ''),
				$typeHintPointer,
				self::CODE_INCORRECT_SPACES_BEFORE_COLON
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($colonPointer - 1, $expectedSpaces);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
