<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ObjectShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHint;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function implode;
use function in_array;
use function lcfirst;
use function sprintf;
use function strtolower;
use const T_CLOSURE;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_FUNCTION;

class ReturnTypeHintSniff implements Sniff
{

	public const CODE_MISSING_ANY_TYPE_HINT = 'MissingAnyTypeHint';

	public const CODE_MISSING_NATIVE_TYPE_HINT = 'MissingNativeTypeHint';

	public const CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION = 'MissingTraversableTypeHintSpecification';

	public const CODE_LESS_SPECIFIC_NATIVE_TYPE_HINT = 'LessSpecificNativeTypeHint';

	public const CODE_USELESS_ANNOTATION = 'UselessAnnotation';

	public const CODE_USELESS_SUPPRESS = 'UselessSuppress';

	private const NAME = 'SlevomatCodingStandard.TypeHints.ReturnTypeHint';

	/** @var bool|null */
	public $enableObjectTypeHint = null;

	/** @var bool|null */
	public $enableStaticTypeHint = null;

	/** @var bool|null */
	public $enableMixedTypeHint = null;

	/** @var bool|null */
	public $enableUnionTypeHint = null;

	/** @var bool|null */
	public $enableIntersectionTypeHint = null;

	/** @var bool|null */
	public $enableNeverTypeHint = null;

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
			T_FUNCTION,
			T_CLOSURE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$this->enableObjectTypeHint = SniffSettingsHelper::isEnabledByPhpVersion($this->enableObjectTypeHint, 70200);
		$this->enableStaticTypeHint = SniffSettingsHelper::isEnabledByPhpVersion($this->enableStaticTypeHint, 80000);
		$this->enableMixedTypeHint = SniffSettingsHelper::isEnabledByPhpVersion($this->enableMixedTypeHint, 80000);
		$this->enableUnionTypeHint = SniffSettingsHelper::isEnabledByPhpVersion($this->enableUnionTypeHint, 80000);
		$this->enableIntersectionTypeHint = SniffSettingsHelper::isEnabledByPhpVersion($this->enableIntersectionTypeHint, 80100);
		$this->enableNeverTypeHint = SniffSettingsHelper::isEnabledByPhpVersion($this->enableNeverTypeHint, 80100);
		$this->enableStandaloneNullTrueFalseTypeHints = SniffSettingsHelper::isEnabledByPhpVersion(
			$this->enableStandaloneNullTrueFalseTypeHints,
			80200
		);

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $pointer, self::NAME)) {
			return;
		}

		if (DocCommentHelper::hasInheritdocAnnotation($phpcsFile, $pointer)) {
			return;
		}

		$token = $phpcsFile->getTokens()[$pointer];

		if ($token['code'] === T_FUNCTION) {
			$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $pointer);
			$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $pointer);
			$prefixedReturnAnnotations = FunctionHelper::getValidPrefixedReturnAnnotations($phpcsFile, $pointer);

			$this->checkFunctionTypeHint($phpcsFile, $pointer, $returnTypeHint, $returnAnnotation, $prefixedReturnAnnotations);
			$this->checkFunctionTraversableTypeHintSpecification(
				$phpcsFile,
				$pointer,
				$returnTypeHint,
				$returnAnnotation,
				$prefixedReturnAnnotations
			);
			$this->checkFunctionUselessAnnotation($phpcsFile, $pointer, $returnTypeHint, $returnAnnotation);
		} elseif ($token['code'] === T_CLOSURE) {
			$this->checkClosureTypeHint($phpcsFile, $pointer);
		}
	}

	/**
	 * @param list<Annotation> $prefixedReturnAnnotations
	 */
	private function checkFunctionTypeHint(
		File $phpcsFile,
		int $functionPointer,
		?TypeHint $returnTypeHint,
		?Annotation $returnAnnotation,
		array $prefixedReturnAnnotations
	): void
	{
		$suppressNameAnyTypeHint = $this->getSniffName(self::CODE_MISSING_ANY_TYPE_HINT);
		$isSuppressedAnyTypeHint = SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $suppressNameAnyTypeHint);

		$suppressNameNativeTypeHint = $this->getSniffName(self::CODE_MISSING_NATIVE_TYPE_HINT);
		$isSuppressedNativeTypeHint = SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $suppressNameNativeTypeHint);

		$hasReturnAnnotation = $this->hasReturnAnnotation($returnAnnotation);
		$returnTypeNode = $this->getReturnTypeNode($returnAnnotation);
		$isAnnotationReturnTypeNever = $returnTypeNode instanceof IdentifierTypeNode
			&& TypeHintHelper::isNeverTypeHint(strtolower($returnTypeNode->name));

		if ($returnTypeHint !== null) {
			$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedAnyTypeHint, $suppressNameAnyTypeHint);
			$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);

			if ($this->enableNeverTypeHint && $returnTypeHint->getTypeHint() === 'void' && $isAnnotationReturnTypeNever) {
				$fix = $phpcsFile->addFixableError(
					sprintf(
						'%s %s() has return type hint "void" but it should be possible to add "never" based on @return annotation "%s".',
						FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
						AnnotationTypeHelper::print($returnTypeNode)
					),
					$functionPointer,
					self::CODE_LESS_SPECIFIC_NATIVE_TYPE_HINT
				);

				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($returnTypeHint->getStartPointer(), 'never');
					$phpcsFile->fixer->endChangeset();
				}
			}

			return;
		}

		$methodsWithoutVoidSupport = ['__construct' => true, '__destruct' => true, '__clone' => true];

		if (array_key_exists(FunctionHelper::getName($phpcsFile, $functionPointer), $methodsWithoutVoidSupport)) {
			$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedAnyTypeHint, $suppressNameAnyTypeHint);
			$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
			return;
		}

		$isAnnotationReturnTypeVoidOrNever = $returnTypeNode instanceof IdentifierTypeNode
			&& (
				TypeHintHelper::isVoidTypeHint(strtolower($returnTypeNode->name))
				|| $isAnnotationReturnTypeNever
			);

		$isAbstract = FunctionHelper::isAbstract($phpcsFile, $functionPointer);
		$returnsValue = $isAbstract
			? ($hasReturnAnnotation && !$isAnnotationReturnTypeVoidOrNever)
			: FunctionHelper::returnsValue($phpcsFile, $functionPointer);

		if ($returnsValue && !$hasReturnAnnotation) {
			if (count($prefixedReturnAnnotations) !== 0) {
				$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedAnyTypeHint, $suppressNameAnyTypeHint);
				return;
			}

			if (!$isSuppressedAnyTypeHint) {
				$phpcsFile->addError(
					sprintf(
						'%s %s() does not have return type hint nor @return annotation for its return value.',
						FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
					),
					$functionPointer,
					self::CODE_MISSING_ANY_TYPE_HINT
				);
			}

			return;
		}

		if (
			!$returnsValue
			&& (
				!$hasReturnAnnotation
				|| $isAnnotationReturnTypeVoidOrNever
			)
		) {
			if (!$isSuppressedNativeTypeHint) {
				$message = !$hasReturnAnnotation
					? sprintf(
						'%s %s() does not have void return type hint.',
						FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
					)
					: sprintf(
						'%s %s() does not have native return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
						FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
						AnnotationTypeHelper::print($returnTypeNode)
					);

				$fix = $phpcsFile->addFixableError($message, $functionPointer, self::getSniffName(self::CODE_MISSING_NATIVE_TYPE_HINT));

				if ($fix) {
					$fixedReturnType = $this->enableNeverTypeHint && $isAnnotationReturnTypeNever
						? 'never'
						: 'void';

					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContent(
						$phpcsFile->getTokens()[$functionPointer]['parenthesis_closer'],
						sprintf(': %s', $fixedReturnType)
					);
					$phpcsFile->fixer->endChangeset();
				}
			}

			return;
		}

		if (!$isSuppressedNativeTypeHint && $returnsValue && $isAnnotationReturnTypeVoidOrNever) {
			$message = sprintf(
				'%s %s() does not have native return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
				FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
				AnnotationTypeHelper::print($returnTypeNode)
			);

			$phpcsFile->addError($message, $functionPointer, self::getSniffName(self::CODE_MISSING_NATIVE_TYPE_HINT));
			return;
		}

		$canTryUnionTypeHint = $this->enableUnionTypeHint && $returnTypeNode instanceof UnionTypeNode;

		$typeHints = [];
		$traversableTypeHints = [];
		$nullableReturnTypeHint = false;

		$originalReturnTypeNode = $returnTypeNode;
		if ($returnTypeNode instanceof NullableTypeNode) {
			$returnTypeNode = $returnTypeNode->type;
		}

		if (AnnotationTypeHelper::containsOneType($returnTypeNode)) {
			/** @var ArrayTypeNode|ArrayShapeNode|ObjectShapeNode|IdentifierTypeNode|ThisTypeNode|GenericTypeNode|CallableTypeNode $returnTypeNode */
			$returnTypeNode = $returnTypeNode;
			$typeHints[] = AnnotationTypeHelper::getTypeHintFromOneType(
				$returnTypeNode,
				false,
				$this->enableStandaloneNullTrueFalseTypeHints
			);

		} elseif ($returnTypeNode instanceof UnionTypeNode || $returnTypeNode instanceof IntersectionTypeNode) {
			$traversableTypeHints = [];
			foreach ($returnTypeNode->types as $typeNode) {
				if (!AnnotationTypeHelper::containsOneType($typeNode)) {
					$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
					return;
				}

				/** @var ArrayTypeNode|ArrayShapeNode|ObjectShapeNode|IdentifierTypeNode|ThisTypeNode|GenericTypeNode|CallableTypeNode $typeNode */
				$typeNode = $typeNode;

				$typeHint = AnnotationTypeHelper::getTypeHintFromOneType($typeNode, $canTryUnionTypeHint);

				if (strtolower($typeHint) === 'null') {
					$nullableReturnTypeHint = true;
					continue;
				}

				$isTraversable = TypeHintHelper::isTraversableType(
					TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHint),
					$this->getTraversableTypeHints()
				);

				if (
					!$typeNode instanceof ArrayTypeNode
					&& !$typeNode instanceof ArrayShapeNode
					&& $isTraversable
				) {
					$traversableTypeHints[] = $typeHint;
				}

				$typeHints[] = $typeHint;
			}

			$traversableTypeHints = array_values(array_unique($traversableTypeHints));
			if (count($traversableTypeHints) > 1 && !$canTryUnionTypeHint) {
				$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
				return;
			}
		}

		$typeHints = array_values(array_unique($typeHints));

		if (count($traversableTypeHints) > 0) {
			/** @var UnionTypeNode|IntersectionTypeNode $returnTypeNode */
			$returnTypeNode = $returnTypeNode;

			$itemsSpecificationTypeHint = AnnotationTypeHelper::getItemsSpecificationTypeFromType($returnTypeNode);
			if ($itemsSpecificationTypeHint !== null) {
				$typeHints = AnnotationTypeHelper::getTraversableTypeHintsFromType(
					$returnTypeNode,
					$phpcsFile,
					$functionPointer,
					$this->getTraversableTypeHints(),
					$canTryUnionTypeHint
				);
			}
		}

		if (count($typeHints) === 0) {
			$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
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
				($returnTypeNode instanceof UnionTypeNode && !$canTryUnionTypeHint)
				|| ($returnTypeNode instanceof IntersectionTypeNode && !$this->enableIntersectionTypeHint)
			)
		) {
			$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
			return;
		}

		foreach ($typeHintsWithConvertedUnion as $typeHintNo => $typeHint) {
			if ($canTryUnionTypeHint && $typeHint === 'false') {
				continue;
			}

			if (!TypeHintHelper::isValidTypeHint(
				$typeHint,
				$this->enableObjectTypeHint,
				$this->enableStaticTypeHint,
				$this->enableMixedTypeHint,
				$this->enableStandaloneNullTrueFalseTypeHints
			)) {
				$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
				return;
			}

			if (TypeHintHelper::isTypeDefinedInAnnotation($phpcsFile, $functionPointer, $typeHint)) {
				$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressedNativeTypeHint, $suppressNameNativeTypeHint);
				return;
			}

			$typeHintsWithConvertedUnion[$typeHintNo] = TypeHintHelper::isVoidTypeHint($typeHint)
				? 'null'
				: TypeHintHelper::convertLongSimpleTypeHintToShort($typeHint);
		}

		if ($originalReturnTypeNode instanceof NullableTypeNode) {
			$nullableReturnTypeHint = true;
		}

		if ($isSuppressedNativeTypeHint) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'%s %s() does not have native return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
				FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
				AnnotationTypeHelper::print($returnTypeNode)
			),
			$functionPointer,
			self::CODE_MISSING_NATIVE_TYPE_HINT
		);
		if (!$fix) {
			return;
		}

		if (in_array('mixed', $typeHintsWithConvertedUnion, true)) {
			$returnTypeHint = 'mixed';
		} elseif ($originalReturnTypeNode instanceof IntersectionTypeNode) {
			$returnTypeHint = implode('&', $typeHintsWithConvertedUnion);
		} else {
			$returnTypeHint = implode('|', $typeHintsWithConvertedUnion);
			if ($nullableReturnTypeHint) {
				if (count($typeHintsWithConvertedUnion) > 1) {
					$returnTypeHint .= '|null';
				} else {
					$returnTypeHint = '?' . $returnTypeHint;
				}
			}
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent(
			$phpcsFile->getTokens()[$functionPointer]['parenthesis_closer'],
			sprintf(': %s', $returnTypeHint)
		);
		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param list<Annotation> $prefixedReturnAnnotations
	 */
	private function checkFunctionTraversableTypeHintSpecification(
		File $phpcsFile,
		int $functionPointer,
		?TypeHint $returnTypeHint,
		?Annotation $returnAnnotation,
		array $prefixedReturnAnnotations
	): void
	{
		$suppressName = $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		$isSuppressed = SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $suppressName);

		$hasTraversableTypeHint = $this->hasTraversableTypeHint($phpcsFile, $functionPointer, $returnTypeHint, $returnAnnotation);
		$hasReturnAnnotation = $this->hasReturnAnnotation($returnAnnotation);

		if (!$hasReturnAnnotation) {
			if ($hasTraversableTypeHint) {
				if (count($prefixedReturnAnnotations) !== 0) {
					$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressed, $suppressName);
					return;
				}

				if (!$isSuppressed) {
					$phpcsFile->addError(
						sprintf(
							'%s %s() does not have @return annotation for its traversable return value.',
							FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
						),
						$functionPointer,
						self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION
					);
				}
			}

			return;
		}

		$returnTypeNode = $this->getReturnTypeNode($returnAnnotation);

		if (
			!$hasTraversableTypeHint
			&& !AnnotationTypeHelper::containsTraversableType(
				$returnTypeNode,
				$phpcsFile,
				$functionPointer,
				$this->getTraversableTypeHints()
			)
		) {
			$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressed, $suppressName);
			return;
		}

		if (AnnotationTypeHelper::containsItemsSpecificationForTraversable(
			$returnTypeNode,
			$phpcsFile,
			$functionPointer,
			$this->getTraversableTypeHints()
		)) {
			$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressed, $suppressName);
			return;
		}

		if ($isSuppressed) {
			return;
		}

		/** @var Annotation $returnAnnotation */
		$returnAnnotation = $returnAnnotation;

		$phpcsFile->addError(
			sprintf(
				'@return annotation of %s %s() does not specify type hint for items of its traversable return value.',
				lcfirst(FunctionHelper::getTypeLabel($phpcsFile, $functionPointer)),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
			),
			$returnAnnotation->getStartPointer(),
			self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION
		);
	}

	private function checkFunctionUselessAnnotation(
		File $phpcsFile,
		int $functionPointer,
		?TypeHint $returnTypeHint,
		?Annotation $returnAnnotation
	): void
	{
		if ($returnAnnotation === null) {
			return;
		}

		$suppressName = self::getSniffName(self::CODE_USELESS_ANNOTATION);
		$isSuppressed = SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $suppressName);

		if (!AnnotationHelper::isAnnotationUseless(
			$phpcsFile,
			$functionPointer,
			$returnTypeHint,
			$returnAnnotation,
			$this->getTraversableTypeHints(),
			$this->enableUnionTypeHint,
			$this->enableIntersectionTypeHint,
			$this->enableStandaloneNullTrueFalseTypeHints
		)) {
			$this->reportUselessSuppress($phpcsFile, $functionPointer, $isSuppressed, $suppressName);
			return;
		}

		if ($isSuppressed) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'%s %s() has useless @return annotation.',
				FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
			),
			$returnAnnotation->getStartPointer(),
			self::CODE_USELESS_ANNOTATION
		);

		if (!$fix) {
			return;
		}

		$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $functionPointer);
		$starPointer = TokenHelper::findPrevious(
			$phpcsFile,
			T_DOC_COMMENT_STAR,
			$returnAnnotation->getStartPointer() - 1,
			$docCommentOpenPointer
		);

		$changeStart = $starPointer ?? $returnAnnotation->getStartPointer();

		/** @var int $changeEnd */
		$changeEnd = TokenHelper::findNext(
			$phpcsFile,
			[T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STAR],
			$returnAnnotation->getEndPointer() + 1
		) - 1;

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::removeBetweenIncluding($phpcsFile, $changeStart, $changeEnd);
		$phpcsFile->fixer->endChangeset();
	}

	private function checkClosureTypeHint(File $phpcsFile, int $closurePointer): void
	{
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $closurePointer);
		$returnsValue = FunctionHelper::returnsValue($phpcsFile, $closurePointer);

		if ($returnsValue || $returnTypeHint !== null) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Closure does not have void return type hint.',
			$closurePointer,
			self::CODE_MISSING_NATIVE_TYPE_HINT
		);

		if (!$fix) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		/** @var int $position */
		$position = TokenHelper::findPreviousEffective($phpcsFile, $tokens[$closurePointer]['scope_opener'] - 1, $closurePointer);

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent($position, ': void');
		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @param Annotation<ReturnTagValueNode>|null $returnAnnotation
	 */
	private function getReturnTypeNode(?Annotation $returnAnnotation): ?TypeNode
	{
		if ($this->hasReturnAnnotation($returnAnnotation)) {
			return $returnAnnotation->getValue()->type;
		}

		return null;
	}

	private function hasTraversableTypeHint(
		File $phpcsFile,
		int $functionPointer,
		?TypeHint $returnTypeHint,
		?Annotation $returnAnnotation
	): bool
	{
		if (
			$returnTypeHint !== null
			&& TypeHintHelper::isTraversableType(
				TypeHintHelper::getFullyQualifiedTypeHint(
					$phpcsFile,
					$functionPointer,
					$returnTypeHint->getTypeHintWithoutNullabilitySymbol()
				),
				$this->getTraversableTypeHints()
			)
		) {
			return true;
		}

		return
			$this->hasReturnAnnotation($returnAnnotation)
			&& AnnotationTypeHelper::containsTraversableType(
				$this->getReturnTypeNode($returnAnnotation),
				$phpcsFile,
				$functionPointer,
				$this->getTraversableTypeHints()
			);
	}

	private function hasReturnAnnotation(?Annotation $returnAnnotation): bool
	{
		return $returnAnnotation !== null && !$returnAnnotation->isInvalid();
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
	 * @return array<int, string>
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

}
