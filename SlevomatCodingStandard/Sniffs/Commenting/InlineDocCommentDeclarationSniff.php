<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use function preg_match;
use function sprintf;
use function substr;
use function trim;
use const T_COMMENT;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_FOREACH;
use const T_VARIABLE;
use const T_WHILE;
use const T_WHITESPACE;

class InlineDocCommentDeclarationSniff implements Sniff
{

	public const CODE_INVALID_FORMAT = 'InvalidFormat';
	public const CODE_INVALID_COMMENT_TYPE = 'InvalidCommentType';

	/**
	 * @return (int|string)[]
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
	 * @param int $commentOpenPointer
	 */
	public function process(File $phpcsFile, $commentOpenPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$commentOpenPointer]['code'] === T_COMMENT) {
			if (preg_match('~^/\*\\s*@var\\s+~', $tokens[$commentOpenPointer]['content']) === 0) {
				return;
			}

			$fix = $phpcsFile->addFixableError(
				'Invalid comment type /* */ for inline documentation comment, use /** */.',
				$commentOpenPointer,
				self::CODE_INVALID_COMMENT_TYPE
			);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($commentOpenPointer, sprintf('/**%s', substr($tokens[$commentOpenPointer]['content'], 2)));
				$phpcsFile->fixer->endChangeset();
			}

			$commentClosePointer = $commentOpenPointer;
			$commentContent = trim(substr($tokens[$commentOpenPointer]['content'], 2, -2));
		} else {
			$commentClosePointer = $tokens[$commentOpenPointer]['comment_closer'];
			$commentContent = trim(TokenHelper::getContent($phpcsFile, $commentOpenPointer + 1, $commentClosePointer - 1));
		}

		if (preg_match('~^@var~', $commentContent) === 0) {
			return;
		}

		$pointerAfterComment = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $commentClosePointer + 1);
		if ($pointerAfterComment === null || !in_array($tokens[$pointerAfterComment]['code'], [T_VARIABLE, T_FOREACH, T_WHILE], true)) {
			return;
		}

		if (preg_match('~^@var\\s+(?:(?:\\S+(?:\\s*[,&\|]\\s*\\S+)+)|\\S+)\\s+\$\\S+(?:\\s+.+)?$~', $commentContent) !== 0) {
			return;
		}

		if (
			preg_match('~^@var\\s+(\$\\S+)\\s+((?:\\S+(?:\\s*[,&\|]\\s*\\S+)+)|\\S+)(\\s+.+)?$~', $commentContent, $matches) !== 0
			&& preg_match('~\\s+~', $matches[2]) === 0
		) {
			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Invalid inline documentation comment format "%s", expected "@var %s %s%s".',
					$commentContent,
					$matches[2],
					$matches[1],
					$matches[3] ?? ''
				),
				$commentOpenPointer,
				self::CODE_INVALID_FORMAT
			);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				for ($i = $commentOpenPointer; $i <= $commentClosePointer; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->addContent(
					$commentOpenPointer,
					sprintf(
						'%s @var %s %s%s */',
						$tokens[$commentOpenPointer]['code'] === T_DOC_COMMENT_OPEN_TAG ? '/**' : '/*',
						$matches[2],
						$matches[1],
						$matches[3] ?? ''
					)
				);
				$phpcsFile->fixer->endChangeset();
			}
		} else {
			$phpcsFile->addError(
				sprintf('Invalid inline documentation comment format "%1$s", expected "@var type $variable".', $commentContent),
				$commentOpenPointer,
				self::CODE_INVALID_FORMAT
			);
		}
	}

}
