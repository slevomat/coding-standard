<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TernaryOperatorHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function ltrim;
use function sprintf;
use function strpos;
use function trim;
use const T_BOOLEAN_NOT;
use const T_COALESCE;
use const T_INLINE_ELSE;
use const T_INLINE_THEN;
use const T_INSTANCEOF;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;
use const T_STRING_CONCAT;
use const T_WHITESPACE;

class RequireShortTernaryOperatorSniff implements Sniff
{

	public const CODE_REQUIRED_SHORT_TERNARY_OPERATOR = 'RequiredShortTernaryOperator';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_INLINE_THEN,
		];
	}

	public function process(File $phpcsFile, int $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);

		if ($tokens[$nextPointer]['code'] === T_INLINE_ELSE) {
			return;
		}

		$conditionStartPointer = TernaryOperatorHelper::getStartPointer($phpcsFile, $inlineThenPointer);
		$inlineElsePointer = TernaryOperatorHelper::getElsePointer($phpcsFile, $inlineThenPointer);
		$inlineElseEndPointer = TernaryOperatorHelper::getEndPointer($phpcsFile, $inlineThenPointer, $inlineElsePointer);

		$thenContent = trim(TokenHelper::getContent($phpcsFile, $inlineThenPointer + 1, $inlineElsePointer - 1));
		$elseContent = trim(TokenHelper::getContent($phpcsFile, $inlineElsePointer + 1, $inlineElseEndPointer));

		$conditionEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $inlineThenPointer - 1);

		$condition = TokenHelper::getContent($phpcsFile, $conditionStartPointer, $conditionEndPointer);

		if ($tokens[$conditionStartPointer]['code'] === T_BOOLEAN_NOT) {
			if ($this->isCompoundCondition($phpcsFile, $conditionStartPointer, $conditionEndPointer)) {
				return;
			}

			if ($elseContent !== ltrim($condition, '!')) {
				return;
			}
		} else {
			if ($thenContent !== $condition) {
				return;
			}
		}

		$fix = $phpcsFile->addFixableError('Use short ternary operator.', $inlineThenPointer, self::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($tokens[$conditionStartPointer]['code'] === T_BOOLEAN_NOT) {
			FixerHelper::replace($phpcsFile, $conditionStartPointer, '');

			$firstPointerInThen = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);
			$pointerBeforeInlineElse = TokenHelper::findPreviousEffective($phpcsFile, $inlineElsePointer - 1);
			$hasUnparenthesizedTernary = TernaryOperatorHelper::hasUnparenthesizedTernary(
				$phpcsFile,
				$firstPointerInThen,
				$pointerBeforeInlineElse,
			);

			$trailingWhitespace = '';
			$lastCommentPointer = TokenHelper::findPrevious(
				$phpcsFile,
				Tokens::$commentTokens,
				$inlineElsePointer - 1,
				$pointerBeforeInlineElse,
			);
			if ($lastCommentPointer !== null) {
				$commentContent = $tokens[$lastCommentPointer]['content'];
				if (strpos($commentContent, '//') === 0 || strpos($commentContent, '#') === 0) {
					$trailingWhitespace = $phpcsFile->eolChar;
					if ($tokens[$inlineElsePointer - 1]['code'] === T_WHITESPACE) {
						$trailingWhitespace .= $tokens[$inlineElsePointer - 1]['content'];
					}
				}
			}

			FixerHelper::change(
				$phpcsFile,
				$inlineThenPointer,
				$inlineElseEndPointer,
				sprintf($hasUnparenthesizedTernary ? '?: (%s%s)' : '?: %s%s', $thenContent, $trailingWhitespace),
			);

		} else {
			FixerHelper::removeBetween($phpcsFile, $inlineThenPointer, $inlineElsePointer);
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * Determines whether the condition is a compound expression where the leading negation does not apply
	 * to the entire condition.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $conditionStartPointer The token pointer to the start of the condition (T_BOOLEAN_NOT).
	 * @param int $conditionEndPointer The token pointer to the end of the condition.
	 * @return bool True if the condition is compound, false if the negation applies to the complete condition.
	 */
	private function isCompoundCondition(File $phpcsFile, int $conditionStartPointer, int $conditionEndPointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$firstPointerAfterNot = TokenHelper::findNextEffective($phpcsFile, $conditionStartPointer + 1);

		if ($tokens[$firstPointerAfterNot]['code'] === T_OPEN_PARENTHESIS) {
			return $tokens[$firstPointerAfterNot]['parenthesis_closer'] !== $conditionEndPointer;
		}

		for ($i = $firstPointerAfterNot; $i <= $conditionEndPointer; $i++) {
			if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
				$i = $tokens[$i]['parenthesis_closer'];
				continue;
			}

			if (in_array($tokens[$i]['code'], [T_OPEN_SHORT_ARRAY, T_OPEN_SQUARE_BRACKET], true)) {
				$i = $tokens[$i]['bracket_closer'];
				continue;
			}

			if ($tokens[$i]['code'] === T_OPEN_CURLY_BRACKET && isset($tokens[$i]['bracket_closer'])) {
				$i = $tokens[$i]['bracket_closer'];
				continue;
			}

			if (
				in_array($tokens[$i]['code'], Tokens::BOOLEAN_OPERATORS, true)
				|| in_array($tokens[$i]['code'], Tokens::$comparisonTokens, true)
				|| in_array($tokens[$i]['code'], Tokens::$operators, true)
				|| in_array($tokens[$i]['code'], [T_COALESCE, T_INLINE_THEN, T_STRING_CONCAT, T_INSTANCEOF], true)
			) {
				return true;
			}
		}

		return false;
	}

}
