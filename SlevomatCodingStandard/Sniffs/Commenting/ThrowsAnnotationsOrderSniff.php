<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_keys;
use function count;
use function sprintf;
use function trim;
use function uasort;
use const T_CLOSURE;
use const T_FUNCTION;

class ThrowsAnnotationsOrderSniff implements Sniff
{

	public const CODE_INCORRECT_ORDER = 'IncorrectOrder';

	public bool $caseSensitive = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_FUNCTION, T_CLOSURE];
	}

	public function process(File $phpcsFile, int $functionPointer): void
	{
		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $functionPointer, '@throws');

		if (count($annotations) <= 1) {
			return;
		}

		$actualOrder = [];
		foreach ($annotations as $key => $annotation) {
			$actualOrder[$key] = $this->getExceptionName($annotation);
		}

		$expectedOrder = $actualOrder;
		uasort($expectedOrder, fn (string $a, string $b): int => NamespaceHelper::compareStatements($a, $b, $this->caseSensitive));

		if ($expectedOrder === $actualOrder) {
			return;
		}

		$firstAnnotation = $annotations[0];

		$fix = $phpcsFile->addFixableError(
			'Incorrect order of @throws annotations.',
			$firstAnnotation->getStartPointer(),
			self::CODE_INCORRECT_ORDER,
		);

		if (!$fix) {
			return;
		}

		$annotationsContent = [];
		foreach ($annotations as $key => $annotation) {
			$annotationsContent[$key] = trim(TokenHelper::getContent(
				$phpcsFile,
				$annotation->getStartPointer(),
				$annotation->getEndPointer(),
			));
		}

		$lastAnnotation = $annotations[count($annotations) - 1];
		$indentation = IndentationHelper::getIndentation($phpcsFile, $firstAnnotation->getStartPointer());

		// Build the replacement content
		$fixedContent = '';
		$isFirst = true;
		foreach (array_keys($expectedOrder) as $key) {
			if ($isFirst) {
				$isFirst = false;
			} else {
				$fixedContent .= sprintf('%s%s', $phpcsFile->eolChar, $indentation);
			}

			$fixedContent .= $annotationsContent[$key];
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change(
			$phpcsFile,
			$firstAnnotation->getStartPointer(),
			$lastAnnotation->getEndPointer(),
			$fixedContent,
		);

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param Annotation<ThrowsTagValueNode> $annotation
	 */
	private function getExceptionName(Annotation $annotation): string
	{
		if ($annotation->isInvalid()) {
			return '';
		}

		$type = $annotation->getValue()->type;

		if ($type instanceof IdentifierTypeNode) {
			return $type->name;
		}

		if ($type instanceof UnionTypeNode && count($type->types) > 0 && $type->types[0] instanceof IdentifierTypeNode) {
			return $type->types[0]->name;
		}

		return AnnotationTypeHelper::print($type);
	}

}
