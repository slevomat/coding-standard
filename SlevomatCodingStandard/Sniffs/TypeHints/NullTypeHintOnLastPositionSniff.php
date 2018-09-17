<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_DOC_COMMENT_OPEN_TAG;
use function array_map;
use function array_merge;
use function array_search;
use function count;
use function explode;
use function implode;
use function in_array;
use function preg_match;
use function preg_replace_callback;
use function preg_split;
use function strtolower;

class NullTypeHintOnLastPositionSniff implements Sniff
{

	public const CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION = 'NullTypeHintNotOnLastPosition';

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
			if (!in_array($annotationName, ['@var', '@param', '@return'], true)) {
				continue;
			}

			foreach ($annotationByName as $annotation) {
				if ($annotation->getContent() === null) {
					continue;
				}

				$typeHintsDefinition = $annotationName === '@var' && preg_match('~^(\$\\S+\\s+)(.+)~', $annotation->getContent(), $matches)
					? $matches[2]
					: preg_split('~\\s+~', $annotation->getContent())[0];

				$typeHints = explode('|', $typeHintsDefinition);
				$lowercasedTypeHints = array_map(function (string $typeHint): string {
					return strtolower($typeHint);
				}, $typeHints);

				$nullPosition = array_search('null', $lowercasedTypeHints, true);

				if ($nullPosition === false) {
					continue;
				}

				if ($nullPosition === count($typeHints) - 1) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(
					'Null type hint should be on last position.',
					$annotation->getStartPointer(),
					self::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION
				);

				if (!$fix) {
					continue;
				}

				$phpcsFile->fixer->beginChangeset();

				for ($i = $annotation->getStartPointer(); $i <= $annotation->getEndPointer(); $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}

				$fixedAnnoationContent = preg_replace_callback(
					'~^((?:(?:@var\\s+\$\\S+)|' . $annotationName . ')\\s+)(\\S+)~',
					function (array $matches) use ($typeHints, $nullPosition): string {
						$nullType = $typeHints[$nullPosition];
						unset($typeHints[$nullPosition]);
						$fixedTypeHints = array_merge($typeHints, [$nullType]);

						return $matches[1] . implode('|', $fixedTypeHints);
					},
					TokenHelper::getContent($phpcsFile, $annotation->getStartPointer(), $annotation->getEndPointer())
				);
				$phpcsFile->fixer->addContent($annotation->getStartPointer(), $fixedAnnoationContent);

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
