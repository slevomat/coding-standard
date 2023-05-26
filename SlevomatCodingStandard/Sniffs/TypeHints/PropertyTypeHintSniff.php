<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ObjectShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHint;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function current;
use function implode;
use function in_array;
use function sprintf;
use function strtolower;
use const T_AS;
use const T_COMMA;
use const T_CONST;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_FUNCTION;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_READONLY;
use const T_SEMICOLON;
use const T_STATIC;
use const T_VAR;
use const T_VARIABLE;

class PropertyTypeHintSniff implements Sniff
{

	public const CODE_MISSING_ANY_TYPE_HINT = 'MissingAnyTypeHint';

	public const CODE_MISSING_NATIVE_TYPE_HINT = 'MissingNativeTypeHint';

	public const CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION = 'MissingTraversableTypeHintSpecification';

	public const CODE_USELESS_ANNOTATION = 'UselessAnnotation';

	public const CODE_USELESS_SUPPRESS = 'UselessSuppress';

	private const NAME = 'SlevomatCodingStandard.TypeHints.PropertyTypeHint';

	/** @var bool|null */
	public $enableNativeTypeHint = null;

	/** @var bool|null */
	public $enableMixedTypeHint = null;

	/** @var bool|null */
	public $enableUnionTypeHint = null;

	/** @var bool|null */
	public $enableIntersectionTypeHint = null;

	/** @var bool|null */
	public $enableStandaloneNullTrueFalseTypeHints = null;

	/** @var list<string> */
	public $traversableTypeHints = [];

	/** @var array<int, string>|null */
	private $normalizedTraversableTypeHints;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_VAR,
			T_PUBLIC,
			T_PROTECTED,
			T_PRIVATE,
			T_STATIC,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$this->enableNativeTypeHint = SniffSettingsHelper::isEnabledByPhpVersion($this->enableNativeTypeHint, 70400);
		$this->enableMixedTypeHint = $this->enableNativeTypeHint
			? SniffSettingsHelper::isEnabledByPhpVersion($this->enableMixedTypeHint, 80000)
			: false;
		$this->enableUnionTypeHint = $this->enableNativeTypeHint
			? SniffSettingsHelper::isEnabledByPhpVersion($this->enableUnionTypeHint, 80000)
			: false;
		$this->enableIntersectionTypeHint = $this->enableNativeTypeHint
			? SniffSettingsHelper::isEnabledByPhpVersion($this->enableIntersectionTypeHint, 80100)
			: false;
		$this->enableStandaloneNullTrueFalseTypeHints = $this->enableNativeTypeHint
			? SniffSettingsHelper::isEnabledByPhpVersion($this->enableStandaloneNullTrueFalseTypeHints, 80200)
			: false;

		$tokens = $phpcsFile->getTokens();

		$asPointer = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 1);
		if ($tokens[$asPointer]['code'] === T_AS) {
			return;
		}

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $pointer + 1);
		if (in_array($tokens[$nextPointer]['code'], [T_VAR, T_PUBLIC, T_PROTECTED, T_PRIVATE, T_READONLY, T_STATIC], true)) {
			// We don't want to report the same property twice
			return;
		}

		$propertyPointer = TokenHelper::findNext($phpcsFile, [T_FUNCTION, T_CONST, T_VARIABLE], $pointer + 1);

		if ($propertyPointer === null || $tokens[$propertyPointer]['code'] !== T_VARIABLE) {
			return;
		}

		if (!PropertyHelper::isProperty($phpcsFile, $propertyPointer)) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, self::NAME)) {
			return;
		}

		$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $propertyPointer);
		if ($docCommentOpenPointer !== null) {
			if (DocCommentHelper::hasInheritdocAnnotation($phpcsFile, $docCommentOpenPointer)) {
				return;
			}

			$varAnnotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer, '@var');
			$prefixedPropertyAnnotations = $this->getValidPrefixedAnnotations($phpcsFile, $docCommentOpenPointer);

			$propertyAnnotation = count($varAnnotations) > 0 ? current($varAnnotations) : null;
		} else {
			$propertyAnnotation = null;
			$prefixedPropertyAnnotations = [];
		}

		$propertyTypeHint = PropertyHelper::findTypeHint($phpcsFile, $propertyPointer);

		$this->checkTypeHint($phpcsFile, $propertyPointer, $propertyTypeHint, $propertyAnnotation, $prefixedPropertyAnnotations);
		$this->checkTraversableTypeHintSpecification(
			$phpcsFile,
			$propertyPointer,
			$propertyTypeHint,
			$propertyAnnotation,
			$prefixedPropertyAnnotations
		);
		$this->checkUselessAnnotation($phpcsFile, $propertyPointer, $propertyTypeHint, $propertyAnnotation);
	}

	/**
	 * @param Annotation<VarTagValueNode|ParamTagValueNode>|null $propertyAnnotation
	 * @param list<Annotation<VarTagValueNode>> $prefixedPropertyAnnotations
	 */
	private function checkTypeHint(
		File $phpcsFile,
		int $propertyPointer,
		?TypeHint $propertyTypeHint,
		?Annotation $propertyAnnotation,
		array $prefixedPropertyAnnotations
	): void
	{
		$suppressNameAnyTypeHint = $this->getSniffName(self::CODE_MISSING_ANY_TYPE_HINT);
		$isSuppressedAnyTypeHint = SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $suppressNameAnyTypeHint);

		$suppressNameNativeTypeHint = $this->getSniffName(self::CODE_MISSING_NATIVE_TYPE_HINT);
		$isSuppressedNativeTypeHint = SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $suppressNameNativeTypeHint);

		if ($propertyTypeHint !== null) {
			$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedAnyTypeHint, $suppressNameAnyTypeHint);
			$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
			return;
		}

		if (!$this->hasAnnotation($propertyAnnotation)) {
			if (count($prefixedPropertyAnnotations) !== 0) {
				$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedAnyTypeHint, $suppressNameAnyTypeHint);
				return;
			}

			if (!$isSuppressedAnyTypeHint) {
				$phpcsFile->addError(
					sprintf(
						$this->enableNativeTypeHint
							? 'Property %s does not have native type hint nor @var annotation for its value.'
							: 'Property %s does not have @var annotation for its value.',
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

		$typeNode = $propertyAnnotation->getValue()->type;
		$originalTypeNode = $typeNode;
		if ($typeNode instanceof NullableTypeNode) {
			$typeNode = $typeNode->type;
		}

		$canTryUnionTypeHint = $this->enableUnionTypeHint && $typeNode instanceof UnionTypeNode;

		$typeHints = [];
		$traversableTypeHints = [];
		$nullableTypeHint = false;

		if (AnnotationTypeHelper::containsOneType($typeNode)) {
			/** @var ArrayTypeNode|ArrayShapeNode|ObjectShapeNode|IdentifierTypeNode|ThisTypeNode|GenericTypeNode|CallableTypeNode $typeNode */
			$typeNode = $typeNode;
			$typeHints[] = AnnotationTypeHelper::getTypeHintFromOneType($typeNode, false, $this->enableStandaloneNullTrueFalseTypeHints);

		} elseif ($typeNode instanceof UnionTypeNode || $typeNode instanceof IntersectionTypeNode) {
			$traversableTypeHints = [];
			foreach ($typeNode->types as $innerTypeNode) {
				if (!AnnotationTypeHelper::containsOneType($innerTypeNode)) {
					$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
					return;
				}

				/** @var ArrayTypeNode|ArrayShapeNode|ObjectShapeNode|IdentifierTypeNode|ThisTypeNode|GenericTypeNode|CallableTypeNode $innerTypeNode */
				$innerTypeNode = $innerTypeNode;

				$typeHint = AnnotationTypeHelper::getTypeHintFromOneType($innerTypeNode, $canTryUnionTypeHint);

				if (strtolower($typeHint) === 'null') {
					$nullableTypeHint = true;
					continue;
				}

				$isTraversable = TypeHintHelper::isTraversableType(
					TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $propertyPointer, $typeHint),
					$this->getTraversableTypeHints()
				);

				if (
					!$innerTypeNode instanceof ArrayTypeNode
					&& !$innerTypeNode instanceof ArrayShapeNode
					&& $isTraversable
				) {
					$traversableTypeHints[] = $typeHint;
				}

				$typeHints[] = $typeHint;
			}

			$traversableTypeHints = array_values(array_unique($traversableTypeHints));
			if (count($traversableTypeHints) > 1 && !$canTryUnionTypeHint) {
				$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
				return;
			}
		}

		$typeHints = array_values(array_unique($typeHints));

		if (count($traversableTypeHints) > 0) {
			/** @var UnionTypeNode|IntersectionTypeNode $typeNode */
			$typeNode = $typeNode;

			$itemsSpecificationTypeHint = AnnotationTypeHelper::getItemsSpecificationTypeFromType($typeNode);
			if ($itemsSpecificationTypeHint !== null) {
				$typeHints = AnnotationTypeHelper::getTraversableTypeHintsFromType(
					$typeNode,
					$phpcsFile,
					$propertyPointer,
					$this->getTraversableTypeHints(),
					$this->enableUnionTypeHint
				);
			}
		}

		if (count($typeHints) === 0) {
			$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
			return;
		}

		$typeHintsWithConvertedUnion = [];
		foreach ($typeHints as $typeHint) {
			if ($this->enableUnionTypeHint && TypeHintHelper::isUnofficialUnionTypeHint($typeHint)) {
				$canTryUnionTypeHint = true;
				$typeHintsWithConvertedUnion = array_merge(
					$typeHintsWithConvertedUnion,
					TypeHintHelper::convertUnofficialUnionTypeHintToOfficialTypeHints($typeHint)
				);
			} else {
				$typeHintsWithConvertedUnion[] = $typeHint;
			}
		}
		$typeHintsWithConvertedUnion = array_unique($typeHintsWithConvertedUnion);

		if (
			count($typeHintsWithConvertedUnion) > 1
			&& (
				($typeNode instanceof UnionTypeNode && !$canTryUnionTypeHint)
				|| ($typeNode instanceof IntersectionTypeNode && !$this->enableIntersectionTypeHint)
			)
		) {
			$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
			return;
		}

		foreach ($typeHintsWithConvertedUnion as $typeHintNo => $typeHint) {
			if ($typeHint === 'callable') {
				$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
				return;
			}

			if ($canTryUnionTypeHint && $typeHint === 'false') {
				continue;
			}

			if (!TypeHintHelper::isValidTypeHint(
				$typeHint,
				true,
				false,
				$this->enableMixedTypeHint,
				$this->enableStandaloneNullTrueFalseTypeHints
			)) {
				$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
				return;
			}

			if (TypeHintHelper::isTypeDefinedInAnnotation($phpcsFile, $propertyPointer, $typeHint)) {
				$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
				return;
			}

			$typeHintsWithConvertedUnion[$typeHintNo] = TypeHintHelper::convertLongSimpleTypeHintToShort($typeHint);
		}

		if ($originalTypeNode instanceof NullableTypeNode) {
			$nullableTypeHint = true;
		}

		if ($isSuppressedNativeTypeHint) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Property %s does not have native type hint for its value but it should be possible to add it based on @var annotation "%s".',
				PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer),
				AnnotationTypeHelper::print($typeNode)
			),
			$propertyPointer,
			self::CODE_MISSING_NATIVE_TYPE_HINT
		);
		if (!$fix) {
			return;
		}

		if (in_array('mixed', $typeHintsWithConvertedUnion, true)) {
			$propertyTypeHint = 'mixed';
		} elseif ($originalTypeNode instanceof IntersectionTypeNode) {
			$propertyTypeHint = implode('&', $typeHintsWithConvertedUnion);
		} else {
			$propertyTypeHint = implode('|', $typeHintsWithConvertedUnion);
			if ($nullableTypeHint) {
				if (count($typeHintsWithConvertedUnion) > 1) {
					$propertyTypeHint .= '|null';
				} else {
					$propertyTypeHint = '?' . $propertyTypeHint;
				}
			}
		}

		$propertyStartPointer = TokenHelper::findPrevious(
			$phpcsFile,
			[T_PRIVATE, T_PROTECTED, T_PUBLIC, T_VAR, T_STATIC, T_READONLY],
			$propertyPointer - 1
		);

		$tokens = $phpcsFile->getTokens();

		$pointerAfterProperty = null;
		if ($nullableTypeHint) {
			$pointerAfterProperty = TokenHelper::findNextEffective($phpcsFile, $propertyPointer + 1);
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent($propertyStartPointer, sprintf(' %s', $propertyTypeHint));

		if (
			$pointerAfterProperty !== null
			&& in_array($tokens[$pointerAfterProperty]['code'], [T_SEMICOLON, T_COMMA], true)
		) {
			$phpcsFile->fixer->addContent($propertyPointer, ' = null');
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param Annotation<VarTagValueNode|ParamTagValueNode>|null $propertyAnnotation
	 * @param list<Annotation<VarTagValueNode>> $prefixedPropertyAnnotations
	 */
	private function checkTraversableTypeHintSpecification(
		File $phpcsFile,
		int $propertyPointer,
		?TypeHint $propertyTypeHint,
		?Annotation $propertyAnnotation,
		array $prefixedPropertyAnnotations
	): void
	{
		$suppressName = $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		$isSuppressed = SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $suppressName);

		$hasTraversableTypeHint = $this->hasTraversableTypeHint($phpcsFile, $propertyPointer, $propertyTypeHint, $propertyAnnotation);
		$hasAnnotation = $this->hasAnnotation($propertyAnnotation);

		if (!$hasAnnotation) {
			if ($hasTraversableTypeHint) {
				if (count($prefixedPropertyAnnotations) !== 0) {
					$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressed, $suppressName);
					return;
				}

				if (!$isSuppressed) {
					$phpcsFile->addError(
						sprintf(
							'@var annotation of property %s does not specify type hint for its items.',
							PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
						),
						$propertyPointer,
						self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION
					);
				}
			}

			return;
		}

		$typeNode = $propertyAnnotation->getValue()->type;

		if (
			!$hasTraversableTypeHint
			&& !AnnotationTypeHelper::containsTraversableType($typeNode, $phpcsFile, $propertyPointer, $this->getTraversableTypeHints())
		) {
			$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressed, $suppressName);
			return;
		}

		if (AnnotationTypeHelper::containsItemsSpecificationForTraversable(
			$typeNode,
			$phpcsFile,
			$propertyPointer,
			$this->getTraversableTypeHints()
		)) {
			$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressed, $suppressName);
			return;
		}

		if ($isSuppressed) {
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

	private function checkUselessAnnotation(
		File $phpcsFile,
		int $propertyPointer,
		?TypeHint $propertyTypeHint,
		?Annotation $propertyAnnotation
	): void
	{
		if ($propertyAnnotation === null) {
			return;
		}

		$suppressName = self::getSniffName(self::CODE_USELESS_ANNOTATION);
		$isSuppressed = SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $suppressName);

		if (!AnnotationHelper::isAnnotationUseless(
			$phpcsFile,
			$propertyPointer,
			$propertyTypeHint,
			$propertyAnnotation,
			$this->getTraversableTypeHints(),
			$this->enableUnionTypeHint,
			$this->enableIntersectionTypeHint,
			$this->enableStandaloneNullTrueFalseTypeHints
		)) {
			$this->reportUselessSuppress($phpcsFile, $propertyPointer, $isSuppressed, $suppressName);
			return;
		}

		if ($isSuppressed) {
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
			$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $propertyPointer);
			$docCommentClosePointer = $phpcsFile->getTokens()[$docCommentOpenPointer]['comment_closer'];

			$changeStart = $docCommentOpenPointer;
			/** @var int $changeEnd */
			$changeEnd = TokenHelper::findNextEffective($phpcsFile, $docCommentClosePointer + 1) - 1;

			$phpcsFile->fixer->beginChangeset();
			FixerHelper::removeBetweenIncluding($phpcsFile, $changeStart, $changeEnd);
			$phpcsFile->fixer->endChangeset();

			return;
		}

		/** @var int $changeStart */
		$changeStart = TokenHelper::findPrevious($phpcsFile, T_DOC_COMMENT_STAR, $propertyAnnotation->getStartPointer() - 1);

		/** @var int $changeEnd */
		$changeEnd = TokenHelper::findNext(
			$phpcsFile,
			[T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STAR],
			$propertyAnnotation->getEndPointer() + 1
		) - 1;

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::removeBetweenIncluding($phpcsFile, $changeStart, $changeEnd);
		$phpcsFile->fixer->endChangeset();
	}

	private function isDocCommentUseless(File $phpcsFile, int $propertyPointer): bool
	{
		if (DocCommentHelper::hasDocCommentDescription($phpcsFile, $propertyPointer)) {
			return false;
		}

		foreach (AnnotationHelper::getAnnotations($phpcsFile, $propertyPointer) as $annotation) {
			if ($annotation->getName() !== '@var') {
				return false;
			}
		}

		return true;
	}

	private function reportUselessSuppress(File $phpcsFile, int $pointer, bool $isSuppressed, string $suppressName): void
	{
		if (!$isSuppressed) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('Useless %s %s', SuppressHelper::ANNOTATION, $suppressName),
			$pointer,
			self::CODE_USELESS_SUPPRESS
		);

		if ($fix) {
			SuppressHelper::removeSuppressAnnotation($phpcsFile, $pointer, $suppressName);
		}
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

	/**
	 * @return list<string>
	 */
	private function getTraversableTypeHints(): array
	{
		if ($this->normalizedTraversableTypeHints === null) {
			$this->normalizedTraversableTypeHints = array_map(static function (string $typeHint): string {
				return NamespaceHelper::isFullyQualifiedName($typeHint)
					? $typeHint
					: sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}, SniffSettingsHelper::normalizeArray($this->traversableTypeHints));
		}
		return $this->normalizedTraversableTypeHints;
	}

	private function hasAnnotation(?Annotation $propertyAnnotation): bool
	{
		return $propertyAnnotation !== null && $propertyAnnotation->getValue() instanceof VarTagValueNode;
	}

	/**
	 * @param Annotation<VarTagValueNode|ParamTagValueNode>|null $propertyAnnotation
	 */
	private function hasTraversableTypeHint(
		File $phpcsFile,
		int $propertyPointer,
		?TypeHint $propertyTypeHint,
		?Annotation $propertyAnnotation
	): bool
	{
		if (
			$propertyTypeHint !== null
			&& TypeHintHelper::isTraversableType(
				TypeHintHelper::getFullyQualifiedTypeHint(
					$phpcsFile,
					$propertyPointer,
					$propertyTypeHint->getTypeHintWithoutNullabilitySymbol()
				),
				$this->getTraversableTypeHints()
			)
		) {
			return true;
		}

		return
			$this->hasAnnotation($propertyAnnotation)
			&& AnnotationTypeHelper::containsTraversableType(
				$propertyAnnotation->getValue()->type,
				$phpcsFile,
				$propertyPointer,
				$this->getTraversableTypeHints()
			);
	}

	/**
	 * @return list<Annotation<VarTagValueNode>>
	 */
	private function getValidPrefixedAnnotations(File $phpcsFile, int $docCommentOpenPointer): array
	{
		$varAnnotations = [];

		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

		foreach (AnnotationHelper::STATIC_ANALYSIS_PREFIXES as $prefix) {
			foreach ($annotations as $annotation) {
				if ($annotation->isInvalid()) {
					continue;
				}

				if ($annotation->getName() === sprintf('@%s-var', $prefix)) {
					$varAnnotations[] = $annotation;
				}
			}
		}

		return $varAnnotations;
	}

}
