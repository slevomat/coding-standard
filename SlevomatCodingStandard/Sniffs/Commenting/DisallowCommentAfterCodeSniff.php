<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\CommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function in_array;
use function strlen;
use function substr;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSURE;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_ELSE;
use const T_ELSEIF;
use const T_OPEN_CURLY_BRACKET;
use const T_WHITESPACE;

class DisallowCommentAfterCodeSniff implements Sniff
{

	public const CODE_DISALLOWED_COMMENT_AFTER_CODE = 'DisallowedCommentAfterCode';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [...TokenHelper::INLINE_COMMENT_TOKEN_CODES, T_DOC_COMMENT_OPEN_TAG];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $commentPointer
	 */
	public function process(File $phpcsFile, $commentPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$commentPointer]['column'] === 1) {
			return;
		}

		$firstNonWhitespacePointerOnLine = TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $commentPointer);

		if ($firstNonWhitespacePointerOnLine === $commentPointer) {
			return;
		}

		if (
			$tokens[$firstNonWhitespacePointerOnLine]['code'] === T_DOC_COMMENT_OPEN_TAG
			&& $tokens[$firstNonWhitespacePointerOnLine]['comment_closer'] > $commentPointer
		) {
			return;
		}

		$commentEndPointer = CommentHelper::getCommentEndPointer($phpcsFile, $commentPointer);
		$nextNonWhitespacePointer = TokenHelper::findNextNonWhitespace($phpcsFile, $commentEndPointer + 1);

		if (
			$nextNonWhitespacePointer !== null
			&& $commentEndPointer !== null
			&& $tokens[$nextNonWhitespacePointer]['line'] === $tokens[$commentEndPointer]['line']

		) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Comment after code is disallowed.', $commentPointer, self::CODE_DISALLOWED_COMMENT_AFTER_CODE);
		if (!$fix) {
			return;
		}

		$commentContent = TokenHelper::getContent($phpcsFile, $commentPointer, $commentEndPointer);
		$commentHasNewLineAtTheEnd = substr($commentContent, -strlen($phpcsFile->eolChar)) === $phpcsFile->eolChar;

		if (!$commentHasNewLineAtTheEnd) {
			$commentContent .= $phpcsFile->eolChar;
		}

		$firstNonWhiteSpacePointerBeforeComment = TokenHelper::findPreviousNonWhitespace($phpcsFile, $commentPointer - 1);

		$newLineAfterComment = $commentHasNewLineAtTheEnd
			? $commentEndPointer
			: TokenHelper::findLastTokenOnLine($phpcsFile, $commentEndPointer);

		$indentation = IndentationHelper::getIndentation($phpcsFile, $firstNonWhitespacePointerOnLine);
		$firstPointerOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $firstNonWhitespacePointerOnLine);

		$phpcsFile->fixer->beginChangeset();

		if (
			$tokens[$firstNonWhiteSpacePointerBeforeComment]['code'] === T_OPEN_CURLY_BRACKET
			&& array_key_exists('scope_condition', $tokens[$firstNonWhiteSpacePointerBeforeComment])
			&& in_array(
				$tokens[$tokens[$firstNonWhiteSpacePointerBeforeComment]['scope_condition']]['code'],
				[T_ELSEIF, T_ELSE, T_CLOSURE],
				true,
			)
		) {
			$phpcsFile->fixer->addContent(
				$firstNonWhiteSpacePointerBeforeComment,
				$phpcsFile->eolChar . IndentationHelper::addIndentation($indentation) . $commentContent,
			);
		} elseif ($tokens[$firstNonWhitespacePointerOnLine]['code'] === T_CLOSE_CURLY_BRACKET) {
			$phpcsFile->fixer->addContent($firstNonWhiteSpacePointerBeforeComment, $phpcsFile->eolChar . $indentation . $commentContent);
		} elseif (isset(Tokens::$stringTokens[$tokens[$firstPointerOnLine]['code']])) {
			$prevNonStringToken = TokenHelper::findPreviousExcluding(
				$phpcsFile,
				[T_WHITESPACE] + Tokens::$stringTokens,
				$firstPointerOnLine - 1,
			);
			$firstTokenOnNonStringTokenLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $prevNonStringToken);
			$firstNonWhitespacePointerOnNonStringTokenLine = TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $prevNonStringToken);
			$prevLineIndentation = IndentationHelper::getIndentation($phpcsFile, $firstNonWhitespacePointerOnNonStringTokenLine);
			$phpcsFile->fixer->addContentBefore($firstTokenOnNonStringTokenLine, $prevLineIndentation . $commentContent);
			$phpcsFile->fixer->addNewline($firstNonWhiteSpacePointerBeforeComment);
		} else {
			$phpcsFile->fixer->addContentBefore($firstPointerOnLine, $indentation . $commentContent);
			$phpcsFile->fixer->addNewline($firstNonWhiteSpacePointerBeforeComment);
		}

		FixerHelper::removeBetweenIncluding($phpcsFile, $firstNonWhiteSpacePointerBeforeComment + 1, $newLineAfterComment);

		$phpcsFile->fixer->endChangeset();
	}

}
