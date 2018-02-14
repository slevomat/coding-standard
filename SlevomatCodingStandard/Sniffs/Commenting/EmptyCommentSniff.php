<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Helpers\TokenHelper;

class EmptyCommentSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_EMPTY_COMMENT = 'EmptyComment';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
			T_COMMENT,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $commentStartPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $commentStartPointer): void
	{
		$commentEndPointer = $this->getCommentEndPointer($phpcsFile, $commentStartPointer);

		if ($commentEndPointer === null) {
			// Part of block comment
			return;
		}

		$commentContent = $this->getCommentContent($phpcsFile, $commentStartPointer, $commentEndPointer);

		$isEmpty = $this->isLineComment($phpcsFile, $commentStartPointer)
			? (bool) preg_match('~^\\s*$~', $commentContent)
			: (bool) preg_match('~^[\\s\*]*$~', $commentContent);

		if (!$isEmpty) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Empty comment',
			$commentStartPointer,
			self::CODE_EMPTY_COMMENT
		);

		if (!$fix) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$phpcsFile->fixer->beginChangeset();

		/** @var int $pointerBeforeWhitespaceBeforeComment */
		$pointerBeforeWhitespaceBeforeComment = TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $commentStartPointer - 1);
		$whitespaceBeforeComment = $pointerBeforeWhitespaceBeforeComment !== $commentStartPointer - 1
			? TokenHelper::getContent($phpcsFile, $pointerBeforeWhitespaceBeforeComment + 1, $commentStartPointer - 1)
			: '';
		$fixedWhitespaceBeforeComment = preg_replace('~[ \\t]+$~', '', $whitespaceBeforeComment);

		for ($i = $pointerBeforeWhitespaceBeforeComment + 1; $i < $commentStartPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->addContent($pointerBeforeWhitespaceBeforeComment, $fixedWhitespaceBeforeComment);

		for ($i = $commentStartPointer; $i <= $commentEndPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$whitespacePointerAfterComment = $commentEndPointer + 1;

		if ($tokens[$pointerBeforeWhitespaceBeforeComment]['line'] === $tokens[$commentStartPointer]['line']) {
			if (substr($tokens[$commentEndPointer]['content'], -strlen($phpcsFile->eolChar)) === $phpcsFile->eolChar) {
				$phpcsFile->fixer->addNewline($commentEndPointer);
			}
		} elseif (
			array_key_exists($whitespacePointerAfterComment, $tokens)
			&& $tokens[$whitespacePointerAfterComment]['code'] === T_WHITESPACE
		) {
			$fixedWhitespaceAfterComment = preg_replace('~^[ \\t]*' . $phpcsFile->eolChar . '~', '', $tokens[$whitespacePointerAfterComment]['content']);
			$phpcsFile->fixer->replaceToken($whitespacePointerAfterComment, $fixedWhitespaceAfterComment);
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function isLineComment(\PHP_CodeSniffer\Files\File $phpcsFile, int $commentPointer): bool
	{
		return (bool) preg_match('~^(?://|#)(.*)~', $phpcsFile->getTokens()[$commentPointer]['content']);
	}

	private function getCommentEndPointer(\PHP_CodeSniffer\Files\File $phpcsFile, int $commentStartPointer): ?int
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$commentStartPointer]['code'] === T_DOC_COMMENT_OPEN_TAG) {
			return $tokens[$commentStartPointer]['comment_closer'];
		}

		if ($this->isLineComment($phpcsFile, $commentStartPointer)) {
			return $commentStartPointer;
		}

		if (strpos($tokens[$commentStartPointer]['content'], '/*') !== 0) {
			// Part of block comment
			return null;
		}

		$nextPointerAfterComment = TokenHelper::findNextExcluding($phpcsFile, T_COMMENT, $commentStartPointer + 1);
		return $nextPointerAfterComment - 1;
	}

	private function getCommentContent(\PHP_CodeSniffer\Files\File $phpcsFile, int $commentStartPointer, int $commentEndPointer): string
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$commentStartPointer]['code'] === T_DOC_COMMENT_OPEN_TAG) {
			return TokenHelper::getContent($phpcsFile, $commentStartPointer + 1, $commentEndPointer - 1);
		}

		if (preg_match('~^(?://|#)(.*)~', $tokens[$commentStartPointer]['content'], $matches)) {
			return $matches[1];
		}

		return substr(TokenHelper::getContent($phpcsFile, $commentStartPointer, $commentEndPointer), 2, -2);
	}

}
