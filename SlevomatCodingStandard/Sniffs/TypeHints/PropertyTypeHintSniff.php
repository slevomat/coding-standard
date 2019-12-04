<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\PropertyTypeHint;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_map;
use function count;
use function in_array;
use function sprintf;
use const PHP_VERSION_ID;
use const T_COMMA;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_SEMICOLON;
use const T_STATIC;
use const T_VAR;
use const T_VARIABLE;

class PropertyTypeHintSniff implements Sniff
{

	private const NAME = 'SlevomatCodingStandard.TypeHints.PropertyTypeHint';

	public const CODE_MISSING_ANY_TYPE_HINT = 'MissingAnyTypeHint';

	public const CODE_MISSING_NATIVE_TYPE_HINT = 'MissingNativeTypeHint';

	public const CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION = 'MissingTraversableTypeHintSpecification';

	public const CODE_USELESS_ANNOTATION = 'UselessAnnotation';

	/** @var bool */
	public $enableNativeTypeHint = PHP_VERSION_ID >= 70400;

	/** @var string[] */
	public $traversableTypeHints = [];

	/** @var array<int, string>|null */
	private $normalizedTraversableTypeHints;

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_VARIABLE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $propertyPointer
	 */
	public function process(File $phpcsFile, $propertyPointer): void
	{
		if (!PropertyHelper::isProperty($phpcsFile, $propertyPointer)) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, self::NAME)) {
			return;
		}

		if (DocCommentHelper::hasInheritdocAnnotation($phpcsFile, $propertyPointer)) {
			return;
		}

		/** @var \SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation[] $varAnnotations */
		$varAnnotations = AnnotationHelper::getAnnotationsByName($phpcsFile, $propertyPointer, '@var');

		$propertyTypeHint = PropertyHelper::findTypeHint($phpcsFile, $propertyPointer);
		$propertyAnnotation = count($varAnnotations) > 0 ? $varAnnotations[0] : null;

		$this->checkTypeHint($phpcsFile, $propertyPointer, $propertyTypeHint, $propertyAnnotation);
		$this->checkTraversableTypeHintSpecification($phpcsFile, $propertyPointer, $propertyTypeHint, $propertyAnnotation);
		$this->checkUselessAnnotation($phpcsFile, $propertyPointer, $propertyTypeHint, $propertyAnnotation);
	}

	private function checkTypeHint(File $phpcsFile, int $propertyPointer, ?PropertyTypeHint $propertyTypeHint, ?VariableAnnotation $propertyAnnotation): void
	{
		if ($propertyTypeHint !== null) {
			return;
		}

		if (!$this->hasAnnotation($propertyAnnotation)) {
			if (!SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, self::getSniffName(self::CODE_MISSING_ANY_TYPE_HINT))) {
				$phpcsFile->addError(
					sprintf(
						$this->enableNativeTypeHint ? 'Property %s does not have native type hint nor @var annotation for its value.' : 'Property %s does not have @var annotation for its value.',
						PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
					),
					$propertyPointer,
					self::CODE_MISSING_ANY_TYPE_HINT
				);
			}

			return;
		}

		if (!$this->enableNativeTypeHint) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, self::getSniffName(self::CODE_MISSING_NATIVE_TYPE_HINT))) {
			return;
		}

		$typeNode = $propertyAnnotation->getType();

		$annotationContainsOneType = AnnotationTypeHelper::containsOneType($typeNode);
		if (
			!$annotationContainsOneType
			&& !AnnotationTypeHelper::containsJustTwoTypes($typeNode)
		) {
			return;
		}

		if ($annotationContainsOneType) {
			/** @var \PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode|\PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $typeNode */
			$typeNode = $typeNode;
			$possibleTypeHint = $typeNode instanceof ArrayTypeNode || $typeNode instanceof ArrayShapeNode
				? 'array'
				: AnnotationTypeHelper::getTypeHintFromOneType($typeNode);
			$nullableTypeHint = false;

		} else {
			/** @var \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode $typeNode */
			$typeNode = $typeNode;

			if (
				!AnnotationTypeHelper::containsNullType($typeNode)
				&& !AnnotationTypeHelper::containsTraversableType($typeNode, $phpcsFile, $propertyPointer, $this->getTraversableTypeHints())
			) {
				return;
			}

			if (AnnotationTypeHelper::containsNullType($typeNode)) {
				/** @var \PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|\PHPStan\PhpDocParser\Ast\Type\GenericTypeNode $notNullTypeHintNode */
				$notNullTypeHintNode = AnnotationTypeHelper::getTypeFromNullableType($typeNode);
				$possibleTypeHint = $notNullTypeHintNode instanceof ArrayTypeNode || $notNullTypeHintNode instanceof ArrayShapeNode
					? 'array'
					: AnnotationTypeHelper::getTypeHintFromOneType($notNullTypeHintNode);
				$nullableTypeHint = true;
			} else {
				$itemsSpecificationTypeHint = AnnotationTypeHelper::getItemsSpecificationTypeFromType($typeNode, $this->getTraversableTypeHints());
				if (!$itemsSpecificationTypeHint instanceof ArrayTypeNode) {
					return;
				}

				$possibleTypeHint = AnnotationTypeHelper::getTraversableTypeHintFromType($typeNode, $this->getTraversableTypeHints());
				$nullableTypeHint = false;

				if (!TypeHintHelper::isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $propertyPointer, $possibleTypeHint), $this->getTraversableTypeHints())) {
					return;
				}
			}
		}

		if ($possibleTypeHint === 'callable') {
			return;
		}

		if (!TypeHintHelper::isValidTypeHint($possibleTypeHint, true)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Property %s does not have native type hint for its value but it should be possible to add it based on @var annotation "%s".',
				PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer),
				AnnotationTypeHelper::export($typeNode)
			),
			$propertyPointer,
			self::CODE_MISSING_NATIVE_TYPE_HINT
		);
		if (!$fix) {
			return;
		}

		$propertyTypeHint = TypeHintHelper::isSimpleTypeHint($possibleTypeHint)
			? TypeHintHelper::convertLongSimpleTypeHintToShort($possibleTypeHint)
			: $possibleTypeHint;

		$propertyStartPointer = TokenHelper::findPrevious($phpcsFile, [T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR, T_STATIC], $propertyPointer - 1);

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent($propertyStartPointer, sprintf(' %s%s', ($nullableTypeHint ? '?' : ''), $propertyTypeHint));

		if ($nullableTypeHint) {
			$pointerAfterProperty = TokenHelper::findNextEffective($phpcsFile, $propertyPointer + 1);
			$tokens = $phpcsFile->getTokens();
			if (in_array($tokens[$pointerAfterProperty]['code'], [T_SEMICOLON, T_COMMA], true)) {
				$phpcsFile->fixer->addContent($propertyPointer, ' = null');
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function checkTraversableTypeHintSpecification(File $phpcsFile, int $propertyPointer, ?PropertyTypeHint $propertyTypeHint, ?VariableAnnotation $propertyAnnotation): void
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION))) {
			return;
		}

		$hasTraversableTypeHint = $this->hasTraversableTypeHint($phpcsFile, $propertyPointer, $propertyTypeHint, $propertyAnnotation);
		$hasAnnotation = $this->hasAnnotation($propertyAnnotation);

		if ($hasTraversableTypeHint && !$hasAnnotation) {
			$phpcsFile->addError(
				sprintf(
					'@var annotation of property %s does not specify type hint for its items.',
					PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
				),
				$propertyPointer,
				self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION
			);

			return;
		}

		if (!$hasAnnotation) {
			return;
		}

		$typeNode = $propertyAnnotation->getType();

		if (!$hasTraversableTypeHint && !AnnotationTypeHelper::containsTraversableType($typeNode, $phpcsFile, $propertyPointer, $this->getTraversableTypeHints())) {
			return;
		}

		if (AnnotationTypeHelper::containsItemsSpecificationForTraversable($typeNode, $phpcsFile, $propertyPointer, $this->getTraversableTypeHints())) {
			return;
		}

		$phpcsFile->addError(
			sprintf(
				'@var annotation of property %s does not specify type hint for its items.',
				PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
			),
			$propertyAnnotation->getStartPointer(),
			self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION
		);
	}

	private function checkUselessAnnotation(File $phpcsFile, int $propertyPointer, ?PropertyTypeHint $propertyTypeHint, ?VariableAnnotation $propertyAnnotation): void
	{
		if ($propertyAnnotation === null) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, self::getSniffName(self::CODE_USELESS_ANNOTATION))) {
			return;
		}

		if (!AnnotationHelper::isAnnotationUseless($phpcsFile, $propertyPointer, $propertyTypeHint, $propertyAnnotation, $this->getTraversableTypeHints())) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Property %s has useless @var annotation.',
				PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
			),
			$propertyAnnotation->getStartPointer(),
			self::CODE_USELESS_ANNOTATION
		);

		if (!$fix) {
			return;
		}

		if ($this->isDocCommentUseless($phpcsFile, $propertyPointer)) {
			/** @var int $docCommentOpenPointer */
			$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $propertyPointer);
			$docCommentClosePointer = $phpcsFile->getTokens()[$docCommentOpenPointer]['comment_closer'];

			$changeStart = $docCommentOpenPointer;
			/** @var int $changeEnd */
			$changeEnd = TokenHelper::findNextEffective($phpcsFile, $docCommentClosePointer + 1) - 1;

			$phpcsFile->fixer->beginChangeset();
			for ($i = $changeStart; $i <= $changeEnd; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
			$phpcsFile->fixer->endChangeset();

			return;
		}

		/** @var int $changeStart */
		$changeStart = TokenHelper::findPrevious($phpcsFile, T_DOC_COMMENT_STAR, $propertyAnnotation->getStartPointer() - 1);
		/** @var int $changeEnd */
		$changeEnd = TokenHelper::findNext($phpcsFile, [T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STAR], $propertyAnnotation->getEndPointer() + 1) - 1;
		$phpcsFile->fixer->beginChangeset();
		for ($i = $changeStart; $i <= $changeEnd; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function isDocCommentUseless(File $phpcsFile, int $propertyPointer): bool
	{
		if (DocCommentHelper::hasDocCommentDescription($phpcsFile, $propertyPointer)) {
			return false;
		}

		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $propertyPointer);
		unset($annotations['@var']);

		return count($annotations) === 0;
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

	/**
	 * @return array<int, string>
	 */
	private function getTraversableTypeHints(): array
	{
		if ($this->normalizedTraversableTypeHints === null) {
			$this->normalizedTraversableTypeHints = array_map(static function (string $typeHint): string {
				return NamespaceHelper::isFullyQualifiedName($typeHint) ? $typeHint : sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}, SniffSettingsHelper::normalizeArray($this->traversableTypeHints));
		}
		return $this->normalizedTraversableTypeHints;
	}

	private function hasAnnotation(?VariableAnnotation $propertyAnnotation): bool
	{
		return $propertyAnnotation !== null && $propertyAnnotation->getContent() !== null && !$propertyAnnotation->isInvalid();
	}

	private function hasTraversableTypeHint(File $phpcsFile, int $propertyPointer, ?PropertyTypeHint $propertyTypeHint, ?VariableAnnotation $propertyAnnotation): bool
	{
		if ($propertyTypeHint !== null && TypeHintHelper::isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $propertyPointer, $propertyTypeHint->getTypeHint()), $this->getTraversableTypeHints())) {
			return true;
		}

		return
			$this->hasAnnotation($propertyAnnotation)
			&& AnnotationTypeHelper::containsTraversableType($propertyAnnotation->getType(), $phpcsFile, $propertyPointer, $this->getTraversableTypeHints());
	}

}
