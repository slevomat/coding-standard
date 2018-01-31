<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;

class ForbiddenCommentsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_COMMENT_FORBIDDEN = 'CommentForbidden';

	/** @var string[] */
	public $forbiddenCommentPatterns = [];

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
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $docCommentOpenPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $file, $docCommentOpenPointer): void
	{
		$comments = DocCommentHelper::getDocCommentDescription($file, $docCommentOpenPointer);

		if ($comments === null) {
			return;
		}

		foreach (SniffSettingsHelper::normalizeArray($this->forbiddenCommentPatterns) as $forbiddenCommentPattern) {
			if (!SniffSettingsHelper::isValidRegularExpression($forbiddenCommentPattern)) {
				continue;
			}

			foreach ($comments as $comment) {
				if (preg_match($forbiddenCommentPattern, $comment->getContent()) === 0) {
					continue;
				}

				$file->addError(
					sprintf('Documentation comment contains forbidden comment "%s".', $comment->getContent()),
					$comment->getPointer(),
					self::CODE_COMMENT_FORBIDDEN
				);
			}
		}
	}

}
