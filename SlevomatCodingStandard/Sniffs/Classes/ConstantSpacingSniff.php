<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use const T_CONST;
use const T_FUNCTION;
use const T_VARIABLE;

class ConstantSpacingSniff extends AbstractPropertyAndConstantSpacing
{

	public const CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT = 'IncorrectCountOfBlankLinesAfterConstant';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_CONST];
	}

	protected function isNextMemberValid(File $phpcsFile, int $pointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$pointer]['code'] === T_CONST) {
			return true;
		}

		$nextPointer = TokenHelper::findNext($phpcsFile, [T_FUNCTION, T_CONST, T_VARIABLE], $pointer + 1);

		return $tokens[$nextPointer]['code'] === T_CONST;
	}

	protected function addError(File $phpcsFile, int $pointer, int $min, int $max, int $found): bool
	{
		$message = 'Expected %d to %d blank lines after constant, found %d.';
		$error = sprintf($message, $min, $max, $found);

		return $phpcsFile->addFixableError($error, $pointer, self::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
	}

}
