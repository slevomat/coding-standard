<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\PhpDocParserHelper;
use function count;
use function sprintf;
use function strtolower;
use const T_DOC_COMMENT_OPEN_TAG;

class NullTypeHintOnLastPositionSniff implements Sniff
{

	public const CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION = 'NullTypeHintNotOnLastPosition';

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
			/** @var list<UnionTypeNode> $unionTypeNodes */
			$unionTypeNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), UnionTypeNode::class);

			foreach ($unionTypeNodes as $unionTypeNode) {
				$nullTypeNode = null;
				$nullPosition = 0;
				$position = 0;
				foreach ($unionTypeNode->types as $typeNode) {
					if ($typeNode instanceof IdentifierTypeNode && strtolower($typeNode->name) === 'null') {
						$nullTypeNode = $typeNode;
						$nullPosition = $position;
						break;
					}

					$position++;
				}

				if ($nullTypeNode === null) {
					continue;
				}

				if ($nullPosition === count($unionTypeNode->types) - 1) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(
					sprintf('Null type hint should be on last position in "%s".', AnnotationTypeHelper::print($unionTypeNode)),
					$annotation->getStartPointer(),
					self::CODE_NULL_TYPE_HINT_NOT_ON_LAST_POSITION
				);

				if (!$fix) {
					continue;
				}

				$fixedTypeNodes = [];
				foreach ($unionTypeNode->types as $typeNode) {
					if ($typeNode === $nullTypeNode) {
						continue;
					}

					$fixedTypeNodes[] = $typeNode;
				}
				$fixedTypeNodes[] = $nullTypeNode;

				$fixedUnionTypeNode = PhpDocParserHelper::cloneNode($unionTypeNode);
				$fixedUnionTypeNode->types = $fixedTypeNodes;

				$phpcsFile->fixer->beginChangeset();

				$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);

				$fixedDocComment = AnnotationHelper::fixAnnotation($parsedDocComment, $annotation, $unionTypeNode, $fixedUnionTypeNode);

				FixerHelper::change(
					$phpcsFile,
					$parsedDocComment->getOpenPointer(),
					$parsedDocComment->getClosePointer(),
					$fixedDocComment
				);

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
