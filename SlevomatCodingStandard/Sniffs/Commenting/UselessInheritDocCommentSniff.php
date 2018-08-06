<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_WHITESPACE;
use const T_WHITESPACE;
use function in_array;
use function preg_match;

class UselessInheritDocCommentSniff implements Sniff
{

	public const CODE_USELESS_INHERIT_DOC_COMMENT = 'UselessInheritDocComment';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $docCommentOpenPointer
	 */
	public function process(File $phpcsFile, $docCommentOpenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$docCommentContent = '';
		for ($i = $docCommentOpenPointer + 1; $i < $tokens[$docCommentOpenPointer]['comment_closer']; $i++) {
			if (in_array($tokens[$i]['code'], [T_DOC_COMMENT_WHITESPACE, T_DOC_COMMENT_STAR], true)) {
				continue;
			}

			$docCommentContent .= $tokens[$i]['content'];
		}

		if (!preg_match('~^(?:\{@inheritDoc\}|@inheritDoc)$~i', $docCommentContent)) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Useless documentation comment with @inheritDoc.', $docCommentOpenPointer, self::CODE_USELESS_INHERIT_DOC_COMMENT);

		if (!$fix) {
			return;
		}

		/** @var int $fixerStart */
		$fixerStart = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, $phpcsFile->eolChar, $docCommentOpenPointer - 1);

		$phpcsFile->fixer->beginChangeset();
		for ($i = $fixerStart; $i <= $tokens[$docCommentOpenPointer]['comment_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

}
