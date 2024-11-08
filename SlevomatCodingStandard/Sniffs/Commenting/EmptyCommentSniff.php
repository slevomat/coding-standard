<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\CommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function preg_match;
use function preg_replace;
use function substr;
use const T_COMMENT;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_WHITESPACE;

class EmptyCommentSniff implements Sniff
{

	public const CODE_EMPTY_COMMENT = 'EmptyComment';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
			T_COMMENT,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $commentStartPointer
	 */
	public function process(File $phpcsFile, $commentStartPointer): void
	{
		$commentEndPointer = CommentHelper::getCommentEndPointer($phpcsFile, $commentStartPointer);

		if ($commentEndPointer === null) {
			// Part of block comment
			return;
		}

		$commentContent = $this->getCommentContent($phpcsFile, $commentStartPointer, $commentEndPointer);

		$isLineComment = CommentHelper::isLineComment($phpcsFile, $commentStartPointer);
		$isEmpty = $this->isEmpty($commentContent, $isLineComment);

		if (!$isEmpty) {
			return;
		}

		if (
			$isLineComment
			&& $this->isPartOfMultiLineInlineComments($phpcsFile, $commentStartPointer, $commentEndPointer)
		) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Empty comment', $commentStartPointer, self::CODE_EMPTY_COMMENT);

		if (!$fix) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		/** @var int $pointerBeforeWhitespaceBeforeComment */
		$pointerBeforeWhitespaceBeforeComment = TokenHelper::findPreviousNonWhitespace($phpcsFile, $commentStartPointer - 1);
		$whitespaceBeforeComment = $pointerBeforeWhitespaceBeforeComment !== $commentStartPointer - 1
			? TokenHelper::getContent($phpcsFile, $pointerBeforeWhitespaceBeforeComment + 1, $commentStartPointer - 1)
			: '';
		$fixedWhitespaceBeforeComment = preg_replace('~[ \\t]+$~', '', $whitespaceBeforeComment);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeWhitespaceBeforeComment, $commentStartPointer);

		$phpcsFile->fixer->addContent($pointerBeforeWhitespaceBeforeComment, $fixedWhitespaceBeforeComment);

		FixerHelper::removeBetweenIncluding($phpcsFile, $commentStartPointer, $commentEndPointer);

		$whitespacePointerAfterComment = $commentEndPointer + 1;

		if ($tokens[$pointerBeforeWhitespaceBeforeComment]['line'] === $tokens[$commentStartPointer]['line']) {
			if (StringHelper::endsWith($tokens[$commentEndPointer]['content'], $phpcsFile->eolChar)) {
				$phpcsFile->fixer->addNewline($commentEndPointer);
			}
		} elseif (
			array_key_exists($whitespacePointerAfterComment, $tokens)
			&& $tokens[$whitespacePointerAfterComment]['code'] === T_WHITESPACE
		) {
			$fixedWhitespaceAfterComment = preg_replace(
				'~^[ \\t]*' . $phpcsFile->eolChar . '~',
				'',
				$tokens[$whitespacePointerAfterComment]['content']
			);
			$phpcsFile->fixer->replaceToken($whitespacePointerAfterComment, $fixedWhitespaceAfterComment);
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function isEmpty(string $comment, bool $isLineComment): bool
	{
		return $isLineComment
			? (bool) preg_match('~^\\s*$~', $comment)
			: (bool) preg_match('~^[\\s\*]*$~', $comment);
	}

	private function getCommentContent(File $phpcsFile, int $commentStartPointer, int $commentEndPointer): string
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$commentStartPointer]['code'] === T_DOC_COMMENT_OPEN_TAG) {
			return TokenHelper::getContent($phpcsFile, $commentStartPointer + 1, $commentEndPointer - 1);
		}

		if (preg_match('~^(?://|#)(.*)~', $tokens[$commentStartPointer]['content'], $matches) === 1) {
			return $matches[1];
		}

		return substr(TokenHelper::getContent($phpcsFile, $commentStartPointer, $commentEndPointer), 2, -2);
	}

	private function isPartOfMultiLineInlineComments(File $phpcsFile, int $commentStartPointer, int $commentEndPointer): bool
	{
		if (!$this->isNonEmptyLineCommentBefore($phpcsFile, $commentStartPointer)) {
			return false;
		}

		return $this->isNonEmptyLineCommentAfter($phpcsFile, $commentEndPointer);
	}

	private function isNonEmptyLineCommentBefore(File $phpcsFile, int $commentStartPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $beforeCommentStartPointer */
		$beforeCommentStartPointer = TokenHelper::findPreviousNonWhitespace($phpcsFile, $commentStartPointer - 1);

		if ($tokens[$beforeCommentStartPointer]['code'] !== T_COMMENT) {
			return false;
		}

		if (!CommentHelper::isLineComment($phpcsFile, $beforeCommentStartPointer)) {
			return false;
		}

		if ($tokens[$beforeCommentStartPointer]['line'] + 1 !== $tokens[$commentStartPointer]['line']) {
			return false;
		}

		/** @var int $beforeCommentEndPointer */
		$beforeCommentEndPointer = CommentHelper::getCommentEndPointer($phpcsFile, $beforeCommentStartPointer);
		if (!$this->isEmpty($this->getCommentContent($phpcsFile, $beforeCommentStartPointer, $beforeCommentEndPointer), true)) {
			return true;
		}

		return $this->isNonEmptyLineCommentBefore($phpcsFile, $beforeCommentStartPointer);
	}

	private function isNonEmptyLineCommentAfter(File $phpcsFile, int $commentEndPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$afterCommentStartPointer = TokenHelper::findNextNonWhitespace($phpcsFile, $commentEndPointer + 1);

		if ($afterCommentStartPointer === null) {
			return false;
		}

		if ($tokens[$afterCommentStartPointer]['code'] !== T_COMMENT) {
			return false;
		}

		if (!CommentHelper::isLineComment($phpcsFile, $afterCommentStartPointer)) {
			return false;
		}

		if ($tokens[$commentEndPointer]['line'] + 1 !== $tokens[$afterCommentStartPointer]['line']) {
			return false;
		}

		/** @var int $afterCommentEndPointer */
		$afterCommentEndPointer = CommentHelper::getCommentEndPointer($phpcsFile, $afterCommentStartPointer);
		if (!$this->isEmpty($this->getCommentContent($phpcsFile, $afterCommentStartPointer, $afterCommentEndPointer), true)) {
			return true;
		}

		return $this->isNonEmptyLineCommentAfter($phpcsFile, $afterCommentEndPointer);
	}

}
