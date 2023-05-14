<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_key_exists;
use function arsort;
use function in_array;
use function key;
use function strnatcasecmp;
use const T_COMMA;
use const T_OPEN_SHORT_ARRAY;

/**
 * @internal
 */
class ArrayHelper
{

	/**
	 * @return list<ArrayKeyValue>
	 */
	public static function parse(File $phpcsFile, int $arrayPointer): array
	{
		$tokens = $phpcsFile->getTokens();

		$arrayToken = $tokens[$arrayPointer];
		[$arrayOpenerPointer, $arrayCloserPointer] = self::openClosePointers($arrayToken);

		$keyValues = [];

		$firstPointerOnNextLine = TokenHelper::findFirstTokenOnNextLine($phpcsFile, $arrayOpenerPointer + 1);
		$firstEffectivePointer = TokenHelper::findNextEffective($phpcsFile, $arrayOpenerPointer + 1);

		$arrayKeyValueStartPointer = $firstPointerOnNextLine !== null && $firstPointerOnNextLine < $firstEffectivePointer
			? $firstPointerOnNextLine
			: $firstEffectivePointer;
		$arrayKeyValueEndPointer = $arrayKeyValueStartPointer;

		$indentation = $tokens[$arrayOpenerPointer]['line'] < $tokens[$firstEffectivePointer]['line']
			? IndentationHelper::getIndentation($phpcsFile, $firstEffectivePointer)
			: '';

		for ($i = $arrayKeyValueStartPointer; $i < $arrayCloserPointer; $i++) {
			$token = $tokens[$i];

			if (in_array($token['code'], TokenHelper::$arrayTokenCodes, true)) {
				$i = self::openClosePointers($token)[1];
				continue;
			}

			if (array_key_exists('scope_closer', $token) && $token['scope_closer'] > $i) {
				$i = $token['scope_closer'] - 1;
				continue;
			}

			if (array_key_exists('parenthesis_closer', $token) && $token['parenthesis_closer'] > $i) {
				$i = $token['parenthesis_closer'] - 1;
				continue;
			}

			$nextEffectivePointer = TokenHelper::findNextEffective($phpcsFile, $i + 1);

			if ($nextEffectivePointer === $arrayCloserPointer) {
				$arrayKeyValueEndPointer = self::getValueEndPointer($phpcsFile, $i, $arrayCloserPointer, $indentation);
				break;
			}

			if ($token['code'] !== T_COMMA || !ScopeHelper::isInSameScope($phpcsFile, $arrayOpenerPointer, $i)) {
				$arrayKeyValueEndPointer = $i;
				continue;
			}

			$arrayKeyValueEndPointer = self::getValueEndPointer($phpcsFile, $i, $arrayCloserPointer, $indentation);

			$keyValues[] = new ArrayKeyValue($phpcsFile, $arrayKeyValueStartPointer, $arrayKeyValueEndPointer);

			$arrayKeyValueStartPointer = $arrayKeyValueEndPointer + 1;
			$i = $arrayKeyValueEndPointer;
		}

		$keyValues[] = new ArrayKeyValue($phpcsFile, $arrayKeyValueStartPointer, $arrayKeyValueEndPointer);

		return $keyValues;
	}

	/**
	 * @param list<ArrayKeyValue> $keyValues
	 */
	public static function getIndentation(array $keyValues): ?string
	{
		$indents = [];
		foreach ($keyValues as $keyValue) {
			$indent = $keyValue->getIndent() ?? 'null';
			$indents[$indent] = isset($indents[$indent])
				? $indents[$indent] + 1
				: 1;
		}
		arsort($indents);
		$indent = key($indents);
		return $indent !== 'null'
			? (string) $indent
			: null;
	}

	/**
	 * @param list<ArrayKeyValue> $keyValues
	 */
	public static function isKeyed(array $keyValues): bool
	{
		foreach ($keyValues as $keyValue) {
			if ($keyValue->getKey() !== null) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param list<ArrayKeyValue> $keyValues
	 */
	public static function isKeyedAll(array $keyValues): bool
	{
		foreach ($keyValues as $keyValue) {
			if (!$keyValue->isUnpacking() && $keyValue->getKey() === null) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Test if non-empty array with opening & closing brackets on separate lines
	 */
	public static function isMultiLine(File $phpcsFile, int $pointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$pointer];
		[$pointerOpener, $pointerCloser] = self::openClosePointers($token);
		$tokenOpener = $tokens[$pointerOpener];
		$tokenCloser = $tokens[$pointerCloser];

		return $tokenOpener['line'] !== $tokenCloser['line'];
	}

	/**
	 * Test if effective tokens between open & closing tokens
	 */
	public static function isNotEmpty(File $phpcsFile, int $pointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$pointer];
		[$pointerOpener, $pointerCloser] = self::openClosePointers($token);

		/** @var int $pointerPreviousToClose */
		$pointerPreviousToClose = TokenHelper::findPreviousEffective($phpcsFile, $pointerCloser - 1);

		return $pointerPreviousToClose !== $pointerOpener;
	}

	/**
	 * @param list<ArrayKeyValue> $keyValues
	 */
	public static function isSortedByKey(array $keyValues): bool
	{
		$previousKey = '';
		foreach ($keyValues as $keyValue) {
			if ($keyValue->isUnpacking()) {
				continue;
			}

			if (strnatcasecmp($previousKey, $keyValue->getKey()) === 1) {
				return false;
			}

			$previousKey = $keyValue->getKey();
		}

		return true;
	}

	/**
	 * @param array<string, array<int, int|string>|int|string> $token
	 * @return array{0: int, 1: int}
	 */
	public static function openClosePointers(array $token): array
	{
		$isShortArray = $token['code'] === T_OPEN_SHORT_ARRAY;
		$pointerOpener = $isShortArray
			? $token['bracket_opener']
			: $token['parenthesis_opener'];
		$pointerCloser = $isShortArray
			? $token['bracket_closer']
			: $token['parenthesis_closer'];
		return [(int) $pointerOpener, (int) $pointerCloser];
	}

	private static function getValueEndPointer(File $phpcsFile, int $endPointer, int $arrayCloserPointer, string $indentation): int
	{
		$tokens = $phpcsFile->getTokens();

		$nextEffectivePointer = TokenHelper::findNextEffective($phpcsFile, $endPointer + 1, $arrayCloserPointer + 1);

		if ($tokens[$nextEffectivePointer]['line'] === $tokens[$endPointer]['line']) {
			return $nextEffectivePointer - 1;
		}

		for ($i = $endPointer + 1; $i < $nextEffectivePointer; $i++) {
			if ($tokens[$i]['line'] === $tokens[$endPointer]['line']) {
				$endPointer = $i;
				continue;
			}

			$nextNonWhitespacePointer = TokenHelper::findNextNonWhitespace($phpcsFile, $i);

			if (!in_array($tokens[$nextNonWhitespacePointer]['code'], TokenHelper::$inlineCommentTokenCodes, true)) {
				break;
			}

			if ($indentation === IndentationHelper::getIndentation($phpcsFile, $nextNonWhitespacePointer)) {
				$endPointer = $i - 1;
				break;
			}

			$i = TokenHelper::findLastTokenOnLine($phpcsFile, $i);
			$endPointer = $i;
		}

		return $endPointer;
	}

}
