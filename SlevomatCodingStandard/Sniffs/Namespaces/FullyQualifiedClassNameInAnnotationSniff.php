<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;

class FullyQualifiedClassNameInAnnotationSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_NON_FULLY_QUALIFIED_CLASS_NAME = 'NonFullyQualifiedClassName';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $annotationTagPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $annotationTagPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$annotationTagName = $tokens[$annotationTagPointer]['content'];

		if (!in_array($annotationTagName, ['@var', '@param', '@return', '@throws'], true)) {
			return;
		}

		$annotationContentPointer = TokenHelper::findNextExcluding($phpcsFile, [T_DOC_COMMENT_WHITESPACE], $annotationTagPointer + 1);
		if ($annotationContentPointer === null || $tokens[$annotationContentPointer]['code'] !== T_DOC_COMMENT_STRING) {
			return;
		}

		$annotationContent = trim($tokens[$annotationContentPointer]['content']);
		if ($annotationTagName === '@var' && preg_match('~^\$\\S+\\s+(.+)~', $annotationContent, $matches)) {
			$typeHintsDefinition = $matches[1];
		} else {
			$typeHintsDefinition = preg_split('~\\s+~', $annotationContent)[0];
		}

		$typeHints = explode('|', $typeHintsDefinition);
		foreach ($typeHints as $typeHint) {
			$typeHint = preg_replace('~(\[\])+$~', '', $typeHint);
			$lowercasedTypeHint = strtolower($typeHint);
			if (
				TypeHintHelper::isSimpleTypeHint($lowercasedTypeHint)
				|| TypeHintHelper::isSimpleUnofficialTypeHints($lowercasedTypeHint)
				|| !TypeHelper::isTypeName($typeHint)
			) {
				continue;
			}

			$fullyQualifiedTypeHint = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $annotationTagPointer, $typeHint);
			if ($fullyQualifiedTypeHint === $typeHint) {
				continue;
			}
			$fix = $phpcsFile->addFixableError(sprintf(
				'Class name %s in %s should be referenced via a fully qualified name.',
				$fullyQualifiedTypeHint,
				$annotationTagName
			), $annotationTagPointer, self::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			$fixedAnnoationContent = preg_replace_callback(
				'~(^|\s|\|)' . preg_quote($typeHint, '~') . '(\s|\||\[|$)~',
				function (array $matches) use ($fullyQualifiedTypeHint): string {
					return $matches[1] . $fullyQualifiedTypeHint . $matches[2];
				},
				$tokens[$annotationContentPointer]['content']
			);
			$phpcsFile->fixer->replaceToken($annotationContentPointer, $fixedAnnoationContent);

			$phpcsFile->fixer->endChangeset();
		}
	}

}
