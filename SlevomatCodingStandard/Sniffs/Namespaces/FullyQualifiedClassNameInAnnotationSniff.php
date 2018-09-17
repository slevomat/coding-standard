<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use const T_DOC_COMMENT_OPEN_TAG;
use function explode;
use function in_array;
use function preg_match;
use function preg_quote;
use function preg_replace;
use function preg_replace_callback;
use function preg_split;
use function sprintf;
use function strtolower;

class FullyQualifiedClassNameInAnnotationSniff implements Sniff
{

	public const CODE_NON_FULLY_QUALIFIED_CLASS_NAME = 'NonFullyQualifiedClassName';

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
	public function process(File $phpcsFile, $docCommentOpenPointer): void
	{
		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

		foreach ($annotations as $annotationName => $annotationByName) {
			if (!in_array($annotationName, ['@var', '@param', '@return', '@throws'], true)) {
				continue;
			}

			foreach ($annotationByName as $annotation) {
				if ($annotation->getContent() === null) {
					continue;
				}

				$typeHintsDefinition = $annotationName === '@var' && preg_match('~^\$\\S+\\s+(.+)~', $annotation->getContent(), $matches)
					? $matches[1]
					: preg_split('~\\s+~', $annotation->getContent())[0];

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

					$fullyQualifiedTypeHint = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $annotation->getStartPointer(), $typeHint);
					if ($fullyQualifiedTypeHint === $typeHint) {
						continue;
					}
					$fix = $phpcsFile->addFixableError(sprintf(
						'Class name %s in %s should be referenced via a fully qualified name.',
						$fullyQualifiedTypeHint,
						$annotationName
					), $annotation->getStartPointer(), self::CODE_NON_FULLY_QUALIFIED_CLASS_NAME);
					if (!$fix) {
						continue;
					}

					$phpcsFile->fixer->beginChangeset();

					for ($i = $annotation->getStartPointer(); $i <= $annotation->getEndPointer(); $i++) {
						$phpcsFile->fixer->replaceToken($i, '');
					}

					$fixedAnnoationContent = preg_replace_callback(
						'~(\s|\|)' . preg_quote($typeHint, '~') . '(\s|\||\[|$)~',
						function (array $matches) use ($fullyQualifiedTypeHint): string {
							return $matches[1] . $fullyQualifiedTypeHint . $matches[2];
						},
						TokenHelper::getContent($phpcsFile, $annotation->getStartPointer(), $annotation->getEndPointer()),
						1
					);
					$phpcsFile->fixer->addContent($annotation->getStartPointer(), $fixedAnnoationContent);

					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

}
