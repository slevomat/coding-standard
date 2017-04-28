<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class InlineDocCommentDeclarationSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_INVALID_FORMAT = 'InvalidFormat';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_VARIABLE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $variablePointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $variablePointer)
	{
		if (PropertyHelper::isProperty($phpcsFile, $variablePointer)) {
			return;
		}

		$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $variablePointer);
		if ($docCommentOpenPointer === null) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$docCommentContent = trim(TokenHelper::getContent($phpcsFile, $docCommentOpenPointer + 1, $tokens[$docCommentOpenPointer]['comment_closer'] - 1));

		if (strpos($docCommentContent, '@var') !== 0) {
			return;
		}

		if (preg_match('~^@var\\s+\\S+\s+\$\\S+(?:\\s+.+)?$~', $docCommentContent)) {
			return;
		}

		if (preg_match('~^@var\\s+(\$\\S+)\\s+(\\S+)(\\s+.+)?$~', $docCommentContent, $matches)) {
			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Invalid inline doc comment format "%s" for variable %s, expected "@var %s %s%s".',
					$docCommentContent,
					$tokens[$variablePointer]['content'],
					$matches[2],
					$matches[1],
					$matches[3] ?? ''
				),
				$variablePointer,
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
				sprintf('Invalid inline doc comment format "%1$s" for variable %2$s, expected "@var type %2$s".', $docCommentContent, $tokens[$variablePointer]['content']),
				$variablePointer,
				self::CODE_INVALID_FORMAT
			);
		}
	}

}
