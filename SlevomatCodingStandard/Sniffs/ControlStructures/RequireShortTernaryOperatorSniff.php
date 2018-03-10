<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class RequireShortTernaryOperatorSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_REQUIRED_SHORT_TERNARY_OPERATOR = 'RequiredShortTernaryOperator';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_INLINE_THEN,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $inlineThenPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $inlineThenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $inlineThenPointer + 1);

		if ($tokens[$nextPointer]['code'] === T_INLINE_ELSE) {
			return;
		}

		$variablePointer = TokenHelper::findPreviousEffective($phpcsFile, $inlineThenPointer - 1);

		if ($tokens[$variablePointer]['code'] !== T_VARIABLE) {
			return;
		}

		/** @var int $pointerBeforeVariable */
		$pointerBeforeVariable = TokenHelper::findPreviousEffective($phpcsFile, $variablePointer - 1);

		if (in_array($tokens[$pointerBeforeVariable]['code'], \PHP_CodeSniffer\Util\Tokens::$comparisonTokens, true)) {
			// Yoda condition
			return;
		}

		$inlineElsePointer = TokenHelper::findNext($phpcsFile, T_INLINE_ELSE, $inlineThenPointer + 1);
		$inlineElseEndPointer = TokenHelper::findNext(
			$phpcsFile,
			[T_SEMICOLON, T_COMMA, T_DOUBLE_ARROW, T_CLOSE_SHORT_ARRAY, T_CLOSE_SQUARE_BRACKET, T_CLOSE_PARENTHESIS, T_COALESCE],
			$inlineElsePointer + 1
		);

		$thenContent = trim(TokenHelper::getContent($phpcsFile, $inlineThenPointer + 1, $inlineElsePointer - 1));

		if ($tokens[$pointerBeforeVariable]['code'] === T_BOOLEAN_NOT) {
			$elseContent = trim(TokenHelper::getContent($phpcsFile, $inlineElsePointer + 1, $inlineElseEndPointer - 1));
			if ($elseContent !== $tokens[$variablePointer]['content']) {
				return;
			}
		} else {
			if ($thenContent !== $tokens[$variablePointer]['content']) {
				return;
			}
		}

		$fix = $phpcsFile->addFixableError('Use short ternary operator.', $inlineThenPointer, self::CODE_REQUIRED_SHORT_TERNARY_OPERATOR);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($tokens[$pointerBeforeVariable]['code'] === T_BOOLEAN_NOT) {
			for ($i = $pointerBeforeVariable; $i < $variablePointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			$pointerBeforeInlineElseEnd = TokenHelper::findPreviousEffective($phpcsFile, $inlineElseEndPointer - 1);

			for ($i = $inlineThenPointer + 1; $i <= $pointerBeforeInlineElseEnd; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			$phpcsFile->fixer->addContent($inlineThenPointer, sprintf(': %s', $thenContent));

		} else {
			for ($i = $inlineThenPointer + 1; $i < $inlineElsePointer; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

}
