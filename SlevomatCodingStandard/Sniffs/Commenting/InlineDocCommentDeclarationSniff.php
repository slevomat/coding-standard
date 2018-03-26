<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class InlineDocCommentDeclarationSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_INVALID_FORMAT = 'InvalidFormat';

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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $docCommentOpenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$pointerAfterDocComment = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $tokens[$docCommentOpenPointer]['comment_closer'] + 1);

		if ($pointerAfterDocComment === null || !in_array($tokens[$pointerAfterDocComment]['code'], [T_VARIABLE, T_FOREACH, T_WHILE], true)) {
			return;
		}

		$docCommentContent = DocCommentHelper::getDocComment($phpcsFile, $docCommentOpenPointer);

		if (strpos($docCommentContent, '@var') !== 0) {
			return;
		}

		if (preg_match('~^@var\\s+\\S+\s+\$\\S+(?:\\s+.+)?$~', $docCommentContent)) {
			return;
		}

		if (preg_match('~^@var\\s+(\$\\S+)\\s+(\\S+)(\\s+.+)?$~', $docCommentContent, $matches)) {
			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Invalid inline doc comment format "%s", expected "@var %s %s%s".',
					$docCommentContent,
					$matches[2],
					$matches[1],
					$matches[3] ?? ''
				),
				$docCommentOpenPointer,
				self::CODE_INVALID_FORMAT
			);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				for ($i = $docCommentOpenPointer; $i <= $tokens[$docCommentOpenPointer]['comment_closer']; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->addContent($docCommentOpenPointer, sprintf('/** @var %s %s%s */', $matches[2], $matches[1], $matches[3] ?? ''));
				$phpcsFile->fixer->endChangeset();
			}
		} else {
			$phpcsFile->addError(
				sprintf('Invalid inline doc comment format "%1$s", expected "@var type $variable".', $docCommentContent),
				$docCommentOpenPointer,
				self::CODE_INVALID_FORMAT
			);
		}
	}

}
