<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class NewWithParenthesesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_MISSING_PARENTHESES = 'MissingParentheses';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_NEW,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $newPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $newPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		/** @var int $nextPointer */
		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $newPointer + 1);

		if ($tokens[$nextPointer]['code'] === T_ANON_CLASS) {
			return;
		}

		$shouldBeOpenParenthesisPointer = $nextPointer + 1;
		do {
			$shouldBeOpenParenthesisPointer = TokenHelper::findNext(
				$phpcsFile,
				[
					T_OPEN_PARENTHESIS,
					T_SEMICOLON,
					T_COMMA,
					T_INLINE_THEN,
					T_INLINE_ELSE,
					T_COALESCE,
					T_CLOSE_SHORT_ARRAY,
					T_CLOSE_SQUARE_BRACKET,
					T_CLOSE_PARENTHESIS,
					T_DOUBLE_ARROW,
				],
				$shouldBeOpenParenthesisPointer
			);

			if (
				$shouldBeOpenParenthesisPointer === null
				|| $tokens[$shouldBeOpenParenthesisPointer]['code'] !== T_CLOSE_SQUARE_BRACKET
				|| $tokens[$shouldBeOpenParenthesisPointer]['bracket_opener'] <= $newPointer
			) {
				break;
			}

			$shouldBeOpenParenthesisPointer++;
		} while (true);

		if ($shouldBeOpenParenthesisPointer !== null && $tokens[$shouldBeOpenParenthesisPointer]['code'] === T_OPEN_PARENTHESIS) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Usage of "new" without parentheses is disallowed.', $newPointer, self::CODE_MISSING_PARENTHESES);
		if (!$fix) {
			return;
		}

		/** @var int $classNameEndPointer */
		$classNameEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $shouldBeOpenParenthesisPointer - 1);

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent($classNameEndPointer, '()');
		$phpcsFile->fixer->endChangeset();
	}

}
