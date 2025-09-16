<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use Exception;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use function array_key_exists;
use function preg_match;
use function preg_replace;
use function sprintf;
use const T_DOC_COMMENT_OPEN_TAG;

class ForbiddenCommentsSniff implements Sniff
{

	public const CODE_COMMENT_FORBIDDEN = 'CommentForbidden';

	/** @var list<string> */
	public array $forbiddenCommentPatterns = [];

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	public function process(File $phpcsFile, int $docCommentOpenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$comments = DocCommentHelper::getDocCommentDescription($phpcsFile, $docCommentOpenPointer);

		if ($comments === null) {
			return;
		}

		foreach (SniffSettingsHelper::normalizeArray($this->forbiddenCommentPatterns) as $forbiddenCommentPattern) {
			if (!SniffSettingsHelper::isValidRegularExpression($forbiddenCommentPattern)) {
				throw new Exception(sprintf('%s is not valid PCRE pattern.', $forbiddenCommentPattern));
			}

			foreach ($comments as $comment) {
				if (preg_match($forbiddenCommentPattern, $comment->getContent()) === 0) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(
					sprintf('Documentation comment contains forbidden comment "%s".', $comment->getContent()),
					$comment->getPointer(),
					self::CODE_COMMENT_FORBIDDEN,
				);

				if (!$fix) {
					continue;
				}

				$phpcsFile->fixer->beginChangeset();

				$fixedDocComment = preg_replace($forbiddenCommentPattern, '', $comment->getContent());

				FixerHelper::replace($phpcsFile, $comment->getPointer(), $fixedDocComment);

				for ($i = $comment->getPointer() - 1; $i > $docCommentOpenPointer; $i--) {
					$contentWithoutSpaces = preg_replace('~ +$~', '', $tokens[$i]['content'], -1, $replacedCount);

					if ($replacedCount === 0) {
						break;
					}

					FixerHelper::replace($phpcsFile, $i, $contentWithoutSpaces);
				}

				$docCommentContent = '';
				for ($i = $docCommentOpenPointer + 1; $i < $tokens[$docCommentOpenPointer]['comment_closer']; $i++) {
					$token = $phpcsFile->fixer->getTokenContent($i);
					$docCommentContent .= $token;
				}

				if (preg_match('~^[\\s\*]*$~', $docCommentContent) !== 0) {
					$pointerBeforeDocComment = $docCommentOpenPointer - 1;
					$contentBeforeWithoutSpaces = preg_replace(
						'~[\t ]+$~',
						'',
						$tokens[$pointerBeforeDocComment]['content'],
						-1,
						$replacedCount,
					);
					if ($replacedCount !== 0) {
						FixerHelper::replace($phpcsFile, $pointerBeforeDocComment, $contentBeforeWithoutSpaces);
					}

					FixerHelper::removeBetweenIncluding(
						$phpcsFile,
						$docCommentOpenPointer,
						$tokens[$docCommentOpenPointer]['comment_closer'],
					);

					$pointerAfterDocComment = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
					if (array_key_exists($pointerAfterDocComment, $tokens)) {
						$contentAfterWithoutSpaces = preg_replace(
							'~^[\r\n]+~',
							'',
							$tokens[$pointerAfterDocComment]['content'],
							-1,
							$replacedCount,
						);
						if ($replacedCount !== 0) {
							FixerHelper::replace($phpcsFile, $pointerAfterDocComment, $contentAfterWithoutSpaces);
						}
					}
				}

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
