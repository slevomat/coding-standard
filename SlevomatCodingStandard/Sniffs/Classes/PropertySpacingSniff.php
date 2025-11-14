<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function sprintf;
use const T_AS;
use const T_CLASS;
use const T_CONST;
use const T_FUNCTION;
use const T_VARIABLE;

class PropertySpacingSniff extends AbstractPropertyConstantAndEnumCaseSpacing
{

	public const CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY = 'IncorrectCountOfBlankLinesAfterProperty';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES;
	}

	public function process(File $phpcsFile, int $pointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$asPointer = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 1);
		if ($tokens[$asPointer]['code'] === T_AS) {
			return $pointer;
		}

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $pointer + 1);
		if (in_array($tokens[$nextPointer]['code'], TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES, true)) {
			// We don't want to report the same property multiple times
			return $nextPointer;
		}

		// Ignore other class members with same modifiers
		$propertyPointer = TokenHelper::findNext($phpcsFile, [T_VARIABLE, T_FUNCTION, T_CONST, T_CLASS], $pointer + 1);
		if (
			$propertyPointer === null
			|| $tokens[$propertyPointer]['code'] !== T_VARIABLE
			|| !PropertyHelper::isProperty($phpcsFile, $propertyPointer)
		) {
			return $propertyPointer ?? $pointer;
		}

		return parent::process($phpcsFile, $propertyPointer);
	}

	protected function isNextMemberValid(File $phpcsFile, int $pointer): bool
	{
		$nextPointer = TokenHelper::findNext($phpcsFile, [T_FUNCTION, T_VARIABLE], $pointer + 1);

		return $nextPointer !== null && $phpcsFile->getTokens()[$nextPointer]['code'] === T_VARIABLE;
	}

	protected function addError(File $phpcsFile, int $pointer, int $minExpectedLines, int $maxExpectedLines, int $found): bool
	{
		if ($minExpectedLines === $maxExpectedLines) {
			$errorMessage = $minExpectedLines === 1
				? 'Expected 1 blank line after property, found %3$d.'
				: 'Expected %2$d blank lines after property, found %3$d.';
		} else {
			$errorMessage = 'Expected %1$d to %2$d blank lines after property, found %3$d.';
		}
		$error = sprintf($errorMessage, $minExpectedLines, $maxExpectedLines, $found);

		return $phpcsFile->addFixableError($error, $pointer, self::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_PROPERTY);
	}

}
