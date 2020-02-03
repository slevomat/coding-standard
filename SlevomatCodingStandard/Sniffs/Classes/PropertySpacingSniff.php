<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use const T_FUNCTION;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_VAR;
use const T_VARIABLE;

class PropertySpacingSniff extends AbstractPropertyAndConstantSpacing
{

	public const CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY = 'IncorrectCountOfBlankLinesAfterProperty';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$propertyPointer = TokenHelper::findNext($phpcsFile, [T_VARIABLE, T_FUNCTION], $pointer + 1);
		if ($propertyPointer === null || $tokens[$propertyPointer]['code'] === T_FUNCTION) {
			return $propertyPointer ?? $pointer;
		}

		return parent::process($phpcsFile, $propertyPointer);
	}

	protected function isNextMemberValid(File $phpcsFile, int $pointer): bool
	{
		$nextPointer = TokenHelper::findNext($phpcsFile, [T_FUNCTION, T_VARIABLE], $pointer + 1);

		return $phpcsFile->getTokens()[$nextPointer]['code'] === T_VARIABLE;
	}

	protected function addError(File $phpcsFile, int $pointer, int $min, int $max, int $found): bool
	{
		$message = 'Expected %d to %d blank lines after property, found %d.';
		$error = sprintf($message, $min, $max, $found);

		return $phpcsFile->addFixableError($error, $pointer, self::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
	}

}
