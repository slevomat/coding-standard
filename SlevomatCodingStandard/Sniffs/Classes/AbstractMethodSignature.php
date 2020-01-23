<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function assert;
use function is_bool;
use function is_int;
use function is_string;
use function preg_replace;
use function preg_replace_callback;
use function rtrim;
use function sprintf;
use function str_repeat;
use function str_replace;
use function strlen;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_TAG;
use const T_SEMICOLON;

/**
 * @internal
 */
abstract class AbstractMethodSignature implements Sniff
{

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_FUNCTION];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $methodPointer
	 * @return array<int, int>
	 */
	protected function getSignatureStartAndEndPointers(File $phpcsFile, int $methodPointer): array
	{
		$signatureStartPointer = $phpcsFile->findFirstOnLine(T_OPEN_TAG, $methodPointer, true);
		assert(!is_bool($signatureStartPointer));

		$pointerAfterSignatureEnd = TokenHelper::findNext($phpcsFile, [T_OPEN_CURLY_BRACKET, T_SEMICOLON], $methodPointer + 1);
		if ($phpcsFile->getTokens()[$pointerAfterSignatureEnd]['code'] === T_SEMICOLON) {
			return [$signatureStartPointer, $pointerAfterSignatureEnd];
		}

		$signatureEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $pointerAfterSignatureEnd - 1);
		assert(is_int($signatureEndPointer));

		return [$signatureStartPointer, $signatureEndPointer];
	}

	protected function getSignature(File $phpcsFile, int $signatureStartPointer, int $signatureEndPointer): string
	{
		$signature = TokenHelper::getContent($phpcsFile, $signatureStartPointer, $signatureEndPointer);
		$signature = preg_replace(sprintf('~%s[ \t]*~', $phpcsFile->eolChar), ' ', $signature);
		assert(is_string($signature));

		$signature = str_replace(['( ', ' )'], ['(', ')'], $signature);
		$signature = rtrim($signature);

		return $signature;
	}

	protected function getSignatureWithoutTabs(string $signature): string
	{
		return preg_replace_callback('~^(\t+)~', static function (array $matches): string {
			return str_repeat('    ', strlen($matches[1]));
		}, $signature);
	}

}
