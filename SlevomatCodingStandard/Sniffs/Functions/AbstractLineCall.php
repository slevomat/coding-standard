<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function preg_replace;
use function preg_replace_callback;
use function rtrim;
use function sprintf;
use function str_repeat;
use function strlen;
use function trim;
use const T_FUNCTION;
use const T_OPEN_PARENTHESIS;
use const T_PARENT;
use const T_SELF;
use const T_STATIC;
use const T_STRING;

abstract class AbstractLineCall implements Sniff
{

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_STRING, T_SELF, T_STATIC, T_PARENT];
	}

	protected function shouldBeSkipped(File $phpcsFile, int $stringPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $stringPointer + 1);

		if ($tokens[$nextPointer]['code'] !== T_OPEN_PARENTHESIS) {
			return true;
		}

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $stringPointer - 1);

		return $tokens[$previousPointer]['code'] === T_FUNCTION;
	}

	protected function getLineStart(File $phpcsFile, int $pointer): string
	{
		$firstPointerOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $pointer);

		return preg_replace_callback('~^(\t+)~', static function (array $matches): string {
			return str_repeat('    ', strlen($matches[1]));
		}, TokenHelper::getContent($phpcsFile, $firstPointerOnLine, $pointer));
	}

	protected function getCall(File $phpcsFile, int $parenthesisOpenerPointer, int $parenthesisCloserPointer): string
	{
		$call = TokenHelper::getContent($phpcsFile, $parenthesisOpenerPointer + 1, $parenthesisCloserPointer - 1);

		$call = preg_replace(sprintf('~%s[ \t]*~', $phpcsFile->eolChar), ' ', $call);
		$call = preg_replace('~([({[])\s+~', '$1', $call);
		$call = preg_replace('~\s+([)}\]])~', '$1', $call);
		$call = preg_replace('~,\s*\)~', ')', $call);
		$call = preg_replace('~,\s*$~', '', $call);

		return trim($call);
	}

	protected function getLineEnd(File $phpcsFile, int $pointer): string
	{
		$firstPointerOnNextLine = TokenHelper::findFirstTokenOnNextLine($phpcsFile, $pointer);

		return rtrim(TokenHelper::getContent($phpcsFile, $pointer, $firstPointerOnNextLine - 1));
	}

}
