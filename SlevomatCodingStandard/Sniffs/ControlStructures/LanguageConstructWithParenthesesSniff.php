<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class LanguageConstructWithParenthesesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_USED_WITH_PARENTHESES = 'UsedWithParentheses';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_BREAK,
			T_CONTINUE,
			T_ECHO,
			T_INCLUDE,
			T_INCLUDE_ONCE,
			T_PRINT,
			T_REQUIRE,
			T_REQUIRE_ONCE,
			T_RETURN,
			T_THROW,
			T_YIELD,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $languageConstructPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $languageConstructPointer)
	{
		$tokens = $phpcsFile->getTokens();

		$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $languageConstructPointer + 1);
		if ($tokens[$nextTokenPointer]['code'] === T_OPEN_PARENTHESIS) {
			$fix = $phpcsFile->addFixableError(sprintf('Usage of language construct "%s" with parentheses is disallowed.', $tokens[$languageConstructPointer]['content']), $languageConstructPointer, self::CODE_USED_WITH_PARENTHESES);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($nextTokenPointer, $tokens[$nextTokenPointer - 1]['code'] === T_WHITESPACE ? '' : ' ');
				$phpcsFile->fixer->replaceToken($tokens[$nextTokenPointer]['parenthesis_closer'], '');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
