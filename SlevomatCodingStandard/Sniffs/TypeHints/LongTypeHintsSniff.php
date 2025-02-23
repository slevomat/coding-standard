<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use function sprintf;
use function strtolower;
use const T_DOC_COMMENT_OPEN_TAG;

class LongTypeHintsSniff implements Sniff
{

	public const CODE_USED_LONG_TYPE_HINT = 'UsedLongTypeHint';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $docCommentOpenPointer
	 */
	public function process(File $phpcsFile, $docCommentOpenPointer): void
	{
		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

		foreach ($annotations as $annotation) {
			$identifierTypeNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), IdentifierTypeNode::class);

			foreach ($identifierTypeNodes as $typeHintNode) {
				$typeHint = $typeHintNode->name;

				$lowercasedTypeHint = strtolower($typeHint);

				$shortTypeHint = null;
				if ($lowercasedTypeHint === 'integer') {
					$shortTypeHint = 'int';
				} elseif ($lowercasedTypeHint === 'boolean') {
					$shortTypeHint = 'bool';
				}

				if ($shortTypeHint === null) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(sprintf(
					'Expected "%s" but found "%s" in %s annotation.',
					$shortTypeHint,
					$typeHint,
					$annotation->getName(),
				), $annotation->getStartPointer(), self::CODE_USED_LONG_TYPE_HINT);

				if (!$fix) {
					continue;
				}

				$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);

				$fixedDocComment = AnnotationHelper::fixAnnotation(
					$parsedDocComment,
					$annotation,
					$typeHintNode,
					new IdentifierTypeNode($shortTypeHint),
				);

				$phpcsFile->fixer->beginChangeset();

				FixerHelper::change(
					$phpcsFile,
					$parsedDocComment->getOpenPointer(),
					$parsedDocComment->getClosePointer(),
					$fixedDocComment,
				);

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
