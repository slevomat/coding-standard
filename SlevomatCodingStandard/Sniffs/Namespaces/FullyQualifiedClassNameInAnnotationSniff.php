<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;

class FullyQualifiedClassNameInAnnotationSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_NON_FULLY_QUALIFIED_CLASS_NAME = 'NonFullyQualifiedClassName';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $annotationTagPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $annotationTagPointer)
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

		$typeHints = explode('|', preg_split('~\\s+~', trim($tokens[$annotationContentPointer]['content']))[0]);
		foreach ($typeHints as $typeHint) {
			$typeHint = preg_replace('~(\[\])+$~', '', $typeHint);
			$lowercasedTypeHint = strtolower($typeHint);
			if (TypeHintHelper::isSimpleTypeHint($lowercasedTypeHint) || in_array($lowercasedTypeHint, TypeHintHelper::$simpleUnofficialTypeHints, true)) {
				continue;
			}

			$fullyQualifiedTypeHint = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $annotationTagPointer, $typeHint);
			if ($fullyQualifiedTypeHint !== $typeHint) {
				$phpcsFile->addError(sprintf(
					'Class name %s in %s should be referenced via a fully qualified name.',
					$fullyQualifiedTypeHint,
					$annotationTagName
				), $annotationTagPointer, self::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
			}
		}
	}

}
