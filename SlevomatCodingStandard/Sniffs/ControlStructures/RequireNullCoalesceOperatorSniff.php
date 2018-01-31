<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class RequireNullCoalesceOperatorSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_NULL_COALESCE_OPERATOR_NOT_USED = 'NullCoalesceOperatorNotUsed';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_ISSET,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $issetPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $issetPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $issetPointer - 1);
		if ($tokens[$previousPointer]['code'] === T_BOOLEAN_NOT) {
			return;
		}

		$openParenthesisPointer = TokenHelper::findNextEffective($phpcsFile, $issetPointer + 1);
		$closeParenthesisPointer = $tokens[$openParenthesisPointer]['parenthesis_closer'];

		$inlineThenPointer = TokenHelper::findNextEffective($phpcsFile, $closeParenthesisPointer + 1);
		if ($tokens[$inlineThenPointer]['code'] !== T_INLINE_THEN) {
			return;
		}

		$commaPointer = TokenHelper::findNext($phpcsFile, T_COMMA, $openParenthesisPointer + 1, $closeParenthesisPointer);
		if ($commaPointer !== null) {
			return;
		}

		$inlineElsePointer = TokenHelper::findNext($phpcsFile, T_INLINE_ELSE, $closeParenthesisPointer + 1);

		$variableContent = trim(TokenHelper::getContent($phpcsFile, $openParenthesisPointer + 1, $closeParenthesisPointer - 1));
		$thenContent = trim(TokenHelper::getContent($phpcsFile, $inlineThenPointer + 1, $inlineElsePointer - 1));

		if ($variableContent !== $thenContent) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use null coalesce operator instead of ternary operator.',
			$issetPointer,
			self::CODE_NULL_COALESCE_OPERATOR_NOT_USED
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		for ($i = $issetPointer; $i <= $inlineElsePointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addContent($issetPointer, sprintf('%s ??', $variableContent));

		$phpcsFile->fixer->endChangeset();
	}

}
