<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use const T_CONST;
use const T_ENUM_CASE;
use const T_FUNCTION;
use const T_USE;
use const T_VARIABLE;

class EnumCaseSpacingSniff extends AbstractPropertyConstantAndEnumCaseSpacing
{

	public const CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_ENUM_CASE = 'IncorrectCountOfBlankLinesAfterEnumCase';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_ENUM_CASE];
	}

	protected function isNextMemberValid(File $phpcsFile, int $pointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] === T_ENUM_CASE) {
			return true;
		}

		$nextPointer = TokenHelper::findNext($phpcsFile, [T_FUNCTION, T_CONST, T_VARIABLE, T_USE, T_ENUM_CASE], $pointer + 1);

		return $nextPointer !== null && $tokens[$nextPointer]['code'] === T_ENUM_CASE;
	}

	protected function addError(File $phpcsFile, int $pointer, int $minExpectedLines, int $maxExpectedLines, int $found): bool
	{
		if ($minExpectedLines === $maxExpectedLines) {
			$errorMessage = $minExpectedLines === 1
				? 'Expected 1 blank line after enum case, found %3$d.'
				: 'Expected %2$d blank lines after enum case, found %3$d.';
		} else {
			$errorMessage = 'Expected %1$d to %2$d blank lines after enum case, found %3$d.';
		}
		$error = sprintf($errorMessage, $minExpectedLines, $maxExpectedLines, $found);

		return $phpcsFile->addFixableError($error, $pointer, self::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_ENUM_CASE);
	}

}
