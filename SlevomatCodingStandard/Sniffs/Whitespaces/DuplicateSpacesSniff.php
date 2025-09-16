<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Whitespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\ParameterHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function preg_match_all;
use function preg_replace;
use function sprintf;
use const PREG_OFFSET_CAPTURE;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_MATCH_ARROW;
use const T_VARIABLE;
use const T_WHITESPACE;

class DuplicateSpacesSniff implements Sniff
{

	public const CODE_DUPLICATE_SPACES = 'DuplicateSpaces';

	public bool $ignoreSpacesBeforeAssignment = false;

	public bool $ignoreSpacesInAnnotation = false;

	public bool $ignoreSpacesInComment = false;

	public bool $ignoreSpacesInParameters = false;

	public bool $ignoreSpacesInMatch = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_WHITESPACE,
			T_DOC_COMMENT_WHITESPACE,
			T_DOC_COMMENT_STRING,
		];
	}

	public function process(File $phpcsFile, int $whitespacePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$whitespacePointer]['column'] === 1) {
			return;
		}

		$content = $tokens[$whitespacePointer]['content'];

		if ($content === $phpcsFile->eolChar) {
			return;
		}

		if ($tokens[$whitespacePointer]['code'] === T_WHITESPACE) {
			if ($this->ignoreSpacesBeforeAssignment) {
				$pointerAfter = TokenHelper::findNextNonWhitespace($phpcsFile, $whitespacePointer + 1);
				if (
					$pointerAfter !== null
					&& in_array($tokens[$pointerAfter]['code'], Tokens::$assignmentTokens, true)
				) {
					return;
				}
			}

			if ($this->ignoreSpacesInParameters) {
				$pointerAfter = TokenHelper::findNextNonWhitespace($phpcsFile, $whitespacePointer + 1);
				if (
					$pointerAfter !== null
					&& $tokens[$pointerAfter]['code'] === T_VARIABLE
					&& ParameterHelper::isParameter($phpcsFile, $pointerAfter)
				) {
					return;
				}
			}

			if ($this->ignoreSpacesInMatch) {
				$pointerAfter = TokenHelper::findNextNonWhitespace($phpcsFile, $whitespacePointer + 1);
				if (
					$pointerAfter !== null
					&& $tokens[$pointerAfter]['code'] === T_MATCH_ARROW
				) {
					return;
				}
			}
		} else {
			if ($this->ignoreSpacesInComment) {
				return;
			}

			if (
				$tokens[$whitespacePointer - 1]['code'] === T_DOC_COMMENT_STAR
				&& $tokens[$whitespacePointer + 1]['code'] === T_DOC_COMMENT_STRING
			) {
				return;
			}

			if ($this->ignoreSpacesInAnnotation) {
				$pointerBefore = TokenHelper::findPrevious($phpcsFile, [T_DOC_COMMENT_OPEN_TAG, T_DOC_COMMENT_TAG], $whitespacePointer - 1);
				if (
					$pointerBefore !== null
					&& $tokens[$pointerBefore]['code'] === T_DOC_COMMENT_TAG
					&& $tokens[$whitespacePointer + 1]['code'] !== T_DOC_COMMENT_CLOSE_TAG
				) {
					return;
				}
			}
		}

		$matchResult = preg_match_all('~ {2,}~', $content, $matches, PREG_OFFSET_CAPTURE);
		if ($matchResult === false || $matchResult === 0) {
			return;
		}

		$fix = false;
		foreach ($matches[0] as [$match, $offset]) {
			$position = $tokens[$whitespacePointer]['column'] + $offset;

			$fixable = $phpcsFile->addFixableError(
				sprintf('Duplicate spaces at position %d.', $position),
				$whitespacePointer,
				self::CODE_DUPLICATE_SPACES,
			);

			if ($fixable) {
				$fix = true;
			}
		}

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::replace($phpcsFile, $whitespacePointer, preg_replace('~ {2,}~', ' ', $content));

		$phpcsFile->fixer->endChangeset();
	}

}
