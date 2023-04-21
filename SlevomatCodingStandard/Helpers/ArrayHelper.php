<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function arsort;
use function end;
use function in_array;
use function key;
use function min;
use function strlen;
use function strnatcasecmp;
use function strpos;
use const T_COMMA;
use const T_OPEN_SHORT_ARRAY;
use const T_WHITESPACE;

/**
 * @internal
 */
class ArrayHelper
{

	/** @var array<int, array<string, array<int, int|string>|int|string>> */
	protected static $tokens;

	/**
	 * @return list<ArrayKeyValue>
	 */
	public static function parse(File $phpcsFile, int $pointer): array
	{
		self::$tokens = $phpcsFile->getTokens();
		$token = self::$tokens[$pointer];
		[$pointerOpener, $pointerCloser] = self::openClosePointers($token);
		$tokenOpener = self::$tokens[$pointerOpener];
		$lineIndents = [''];
		$keyValues = [];
		$skipUntilPointer = null;
		$tokenEnd = null;
		$pointer = $pointerOpener + 1;
		for (; $pointer < $pointerCloser; $pointer++) {
			$token = self::$tokens[$pointer];
			if ($token['line'] > $tokenOpener['line'] || in_array($token['code'], TokenHelper::$ineffectiveTokenCodes, true) === false) {
				break;
			}
		}
		$pointerStart = $pointer;

		for (; $pointer < $pointerCloser; $pointer++) {
			if ($pointer < $skipUntilPointer) {
				continue;
			}

			$token = self::$tokens[$pointer];

			if (in_array($token['code'], TokenHelper::$arrayTokenCodes, true)) {
				$pointerCloserWalk = self::openClosePointers($token)[1];
				$skipUntilPointer = $pointerCloserWalk;
				continue;
			}
			if (isset($token['scope_closer']) && $token['scope_closer'] > $pointer) {
				$skipUntilPointer = $token['scope_closer'];
				continue;
			}
			if (isset($token['parenthesis_closer'])) {
				$skipUntilPointer = $token['parenthesis_closer'];
				continue;
			}
			$nextEffective = in_array($token['code'], TokenHelper::$ineffectiveTokenCodes, true)
				? TokenHelper::findNextEffective($phpcsFile, $pointer)
				: TokenHelper::findNextEffective($phpcsFile, $pointer + 1);
			if (
				isset($lineIndents[$token['line']]) === false
				&& in_array($token['code'], TokenHelper::$ineffectiveTokenCodes, true) === false
			) {
				$firstPointerOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $pointer);
				$firstEffective = TokenHelper::findNextEffective($phpcsFile, $firstPointerOnLine);
				$lineIndents[$token['line']] = TokenHelper::getContent($phpcsFile, $firstPointerOnLine, $firstEffective - 1);
			}

			$startNewKeyValue = $tokenEnd !== null
				? self::parseTestStartKeyVal($pointer, $tokenEnd, end($lineIndents))
				: false;
			if ($startNewKeyValue) {
				if ($nextEffective === $pointerCloser && in_array($token['code'], TokenHelper::$ineffectiveTokenCodes, true)) {
					// there are no more key/values
					$firstPointerOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $pointer);
					if ($pointer === $firstPointerOnLine) {
						// end last key/value on the prev token
						$pointer--;
					}
					break;
				}
				$startNewKeyValue = false;
				$tokenEnd = null;
				$keyValues[] = new ArrayKeyValue($phpcsFile, $pointerStart, $pointer - 1);
				$pointerStart = $pointer;
			}
			if ($token['code'] === T_COMMA || $tokenEnd !== null) {
				$tokenEnd = $token;
			}
		}

		$pointer = min($pointer, $pointerCloser - 1);
		$keyValues[] = new ArrayKeyValue($phpcsFile, $pointerStart, $pointer);

		self::$tokens = [];
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

	/**
	 * Test whether we should begin collecting the next key/value tokens
	 *
	 * @param array<string, array<int, int|string>|int|string> $tokenPrev
	 */
	private static function parseTestStartKeyVal(int $pointer, array $tokenPrev, string $lastIndent): bool
	{
		$token = self::$tokens[$pointer];

		// token['column'] cannot be relied on if tabs are being used
		//   this simply checks if indent contains tab and is the same as the prev indent plus additional
		$testTabIndent = static function ($indent, $indentPrev) {
			return strpos($indent, "\t") !== false
				&& strpos($indent, $indentPrev) === 0
				&& strlen($indent) > strlen($indentPrev);
		};

		$startNew = true;

		if (
			in_array($token['code'], TokenHelper::$ineffectiveTokenCodes, true)
			&& $token['line'] === $tokenPrev['line']
		) {
			// we're whitespace or comment after the value...
			$startNew = false;
		} elseif (
			$token['code'] === T_WHITESPACE
			&& in_array($tokenPrev['code'], TokenHelper::$inlineCommentTokenCodes, true)
			&& in_array(self::$tokens[$pointer + 1]['code'], TokenHelper::$inlineCommentTokenCodes, true)
			&& (self::$tokens[$pointer + 1]['column'] >= $tokenPrev['column']
				|| $testTabIndent($token['content'], $lastIndent)
			)
		) {
			// 'key' => 'value' // tokenPrev is this comment
			//                  // we're in the preceeding whitespace
			$startNew = false;
		}

		return $startNew;
	}

}
