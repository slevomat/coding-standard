<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\ReturnTypeHint;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_flip;
use function array_key_exists;
use function array_map;
use function count;
use function in_array;
use function lcfirst;
use function sprintf;
use function stripos;
use function strtolower;
use const PHP_VERSION_ID;
use const T_BITWISE_AND;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_ELLIPSIS;
use const T_FUNCTION;
use const T_VARIABLE;

class TypeHintDeclarationSniff implements Sniff
{

	private const NAME = 'SlevomatCodingStandard.TypeHints.TypeHintDeclaration';

	public const CODE_MISSING_PARAMETER_TYPE_HINT = 'MissingParameterTypeHint';

	public const CODE_MISSING_PROPERTY_TYPE_HINT = 'MissingPropertyTypeHint';

	public const CODE_MISSING_RETURN_TYPE_HINT = 'MissingReturnTypeHint';

	public const CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION = 'MissingTraversableParameterTypeHintSpecification';

	public const CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION = 'MissingTraversablePropertyTypeHintSpecification';

	public const CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION = 'MissingTraversableReturnTypeHintSpecification';

	public const CODE_USELESS_PARAMETER_ANNOTATION = 'UselessParameterAnnotation';

	public const CODE_USELESS_RETURN_ANNOTATION = 'UselessReturnAnnotation';

	public const CODE_INCORRECT_RETURN_TYPE_HINT = 'IncorrectReturnTypeHint';

	public const CODE_USELESS_DOC_COMMENT = 'UselessDocComment';

	/**
	 * @deprecated
	 * @var bool
	 */
	public $enableObjectTypeHint = PHP_VERSION_ID >= 70200;

	/** @var bool */
	public $enableEachParameterAndReturnInspection = false;

	/** @var string[] */
	public $traversableTypeHints = [];

	/** @var bool */
	public $allAnnotationsAreUseful = false;

	/** @var array<string, int>|null */
	private $normalizedTraversableTypeHints;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$token = $phpcsFile->getTokens()[$pointer];

		if ($token['code'] === T_FUNCTION) {
			$this->checkParametersTypeHints($phpcsFile, $pointer);
			$this->checkReturnTypeHints($phpcsFile, $pointer);
			$this->checkUselessDocComment($phpcsFile, $pointer);
		} elseif ($token['code'] === T_CLOSURE) {
			$this->checkClosure($phpcsFile, $pointer);
		} elseif ($token['code'] === T_VARIABLE && PropertyHelper::isProperty($phpcsFile, $pointer)) {
			$this->checkPropertyTypeHint($phpcsFile, $pointer);
		}
	}

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_CLOSURE,
			T_VARIABLE,
		];
	}

	private function checkParametersTypeHints(File $phpcsFile, int $functionPointer): void
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, self::NAME)) {
			return;
		}

		if ($this->hasInheritdocAnnotation($phpcsFile, $functionPointer)) {
			return;
		}

		$parametersAnnotations = $this->getFunctionParametersAnnotations($phpcsFile, $functionPointer);

		if (!SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION))) {
			foreach (FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer) as $parameterName => $parameterTypeHint) {
				$hasTraversableTypeHint = false;
				if ($parameterTypeHint !== null && $this->isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint()))) {
					$hasTraversableTypeHint = true;
				} elseif (array_key_exists($parameterName, $parametersAnnotations) && $this->annotationContainsTraversableType($phpcsFile, $functionPointer, $parametersAnnotations[$parameterName]->getType())) {
					$hasTraversableTypeHint = true;
				}

				if ($hasTraversableTypeHint && !array_key_exists($parameterName, $parametersAnnotations)) {
					$phpcsFile->addError(
						sprintf(
							'%s %s() does not have @param annotation for its traversable parameter %s.',
							$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
							$parameterName
						),
						$functionPointer,
						self::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION
					);
				} elseif (array_key_exists($parameterName, $parametersAnnotations)) {
					$parameterTypeNode = $parametersAnnotations[$parameterName]->getType();

					if (
						(
							$hasTraversableTypeHint
							|| $this->annotationContainsTraversableType($phpcsFile, $functionPointer, $parameterTypeNode)
						)
						&& !$this->annotationContainsItemsSpecificationForTraversable($phpcsFile, $functionPointer, $parameterTypeNode)
					) {
						$phpcsFile->addError(
							sprintf(
								'@param annotation of %s %s() does not specify type hint for items of its traversable parameter %s.',
								lcfirst($this->getFunctionTypeLabel($phpcsFile, $functionPointer)),
								FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
								$parameterName
							),
							$parametersAnnotations[$parameterName]->getStartPointer(),
							self::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION
						);
					}
				}
			}
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_PARAMETER_TYPE_HINT))) {
			return;
		}

		foreach (FunctionHelper::getParametersWithoutTypeHint($phpcsFile, $functionPointer) as $parameterName) {
			if (!array_key_exists($parameterName, $parametersAnnotations)) {
				$phpcsFile->addError(
					sprintf(
						'%s %s() does not have parameter type hint nor @param annotation for its parameter %s.',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
						$parameterName
					),
					$functionPointer,
					self::CODE_MISSING_PARAMETER_TYPE_HINT
				);

				continue;
			}

			$parameterTypeNode = $parametersAnnotations[$parameterName]->getType();

			if ($parameterTypeNode instanceof IdentifierTypeNode && strtolower($parameterTypeNode->name) === 'null') {
				continue;
			}

			$annotationContainsOneType = $this->annotationContainsOneType($parameterTypeNode);
			if (
				!$annotationContainsOneType
				&& !$this->annotationContainsJustTwoTypes($parameterTypeNode)
			) {
				continue;
			}

			if ($annotationContainsOneType) {
				/** @var \PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|\PHPStan\PhpDocParser\Ast\Type\GenericTypeNode $parameterTypeNode */
				$parameterTypeNode = $parameterTypeNode;
				$possibleParameterTypeHint = $parameterTypeNode instanceof ArrayTypeNode ? 'array' : $this->getTypeHintFromOneType($parameterTypeNode);
				$nullableParameterTypeHint = false;

			} else {
				/** @var \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode $parameterTypeNode */
				$parameterTypeNode = $parameterTypeNode;

				if (
					!$this->annotationTypeContainsNullType($parameterTypeNode)
					&& !$this->annotationContainsTraversableType($phpcsFile, $functionPointer, $parameterTypeNode)
				) {
					continue;
				}

				if ($this->annotationTypeContainsNullType($parameterTypeNode)) {
					/** @var \PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|\PHPStan\PhpDocParser\Ast\Type\GenericTypeNode $notNullTypeHintNode */
					$notNullTypeHintNode = $this->getTypeFromNullableType($parameterTypeNode);
					$possibleParameterTypeHint = $notNullTypeHintNode instanceof ArrayTypeNode ? 'array' : $this->getTypeHintFromOneType($notNullTypeHintNode);
					$nullableParameterTypeHint = true;
				} else {

					$itemsSpecificationTypeHint = $this->getItemsSpecificationTypeFromType($parameterTypeNode);
					if (!$itemsSpecificationTypeHint instanceof ArrayTypeNode) {
						continue;
					}

					$possibleParameterTypeHint = $this->getTraversableTypeHintFromType($parameterTypeNode);
					$nullableParameterTypeHint = false;

					if (!$this->isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $possibleParameterTypeHint))) {
						continue;
					}
				}
			}

			if (!$this->isValidTypeHint($possibleParameterTypeHint)) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'%s %s() does not have parameter type hint for its parameter %s but it should be possible to add it based on @param annotation "%s".',
					$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
					FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
					$parameterName,
					AnnotationTypeHelper::export($parameterTypeNode)
				),
				$functionPointer,
				self::CODE_MISSING_PARAMETER_TYPE_HINT
			);
			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			$parameterTypeHint = TypeHintHelper::isSimpleTypeHint($possibleParameterTypeHint)
				? TypeHintHelper::convertLongSimpleTypeHintToShort($possibleParameterTypeHint)
				: $possibleParameterTypeHint;

			$tokens = $phpcsFile->getTokens();
			/** @var int $parameterPointer */
			$parameterPointer = TokenHelper::findNextContent($phpcsFile, T_VARIABLE, $parameterName, $tokens[$functionPointer]['parenthesis_opener'], $tokens[$functionPointer]['parenthesis_closer']);

			$beforeParameterPointer = $parameterPointer;
			do {
				$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $beforeParameterPointer - 1, $tokens[$functionPointer]['parenthesis_opener'] + 1);
				if ($previousPointer === null || !in_array($tokens[$previousPointer]['code'], [T_BITWISE_AND, T_ELLIPSIS], true)) {
					break;
				}

				/** @var int $beforeParameterPointer */
				$beforeParameterPointer = $previousPointer;
			} while (true);

			$phpcsFile->fixer->addContentBefore($beforeParameterPointer, sprintf('%s%s ', ($nullableParameterTypeHint ? '?' : ''), $parameterTypeHint));

			$phpcsFile->fixer->endChangeset();
		}
	}

	private function checkReturnTypeHints(File $phpcsFile, int $functionPointer): void
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, self::NAME)) {
			return;
		}

		if ($this->hasInheritdocAnnotation($phpcsFile, $functionPointer)) {
			return;
		}

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);

		if ($returnAnnotation !== null && $returnAnnotation->getContent() !== null && !$returnAnnotation->isInvalid()) {
			$hasReturnAnnotation = true;
			$returnTypeNode = $returnAnnotation->getType();
			$isAnnotationReturnTypeVoid = $returnTypeNode instanceof IdentifierTypeNode && strtolower($returnTypeNode->name) === 'void';
		} else {
			$hasReturnAnnotation = false;
			$returnTypeNode = null;
			$isAnnotationReturnTypeVoid = false;
		}

		$hasTraversableTypeHint = false;
		if ($returnTypeHint !== null && $this->isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint()))) {
			$hasTraversableTypeHint = true;
		} elseif ($hasReturnAnnotation && $this->annotationContainsTraversableType($phpcsFile, $functionPointer, $returnTypeNode)) {
			$hasTraversableTypeHint = true;
		}

		if (!SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION))) {
			if ($hasTraversableTypeHint && !$hasReturnAnnotation) {
				$phpcsFile->addError(
					sprintf(
						'%s %s() does not have @return annotation for its traversable return value.',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
					),
					$functionPointer,
					self::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION
				);
			} elseif ($hasReturnAnnotation) {
				if (
					(
						$hasTraversableTypeHint
						|| $this->annotationContainsTraversableType($phpcsFile, $functionPointer, $returnTypeNode)
					)
					&& !$this->annotationContainsItemsSpecificationForTraversable($phpcsFile, $functionPointer, $returnTypeNode)
				) {
					/** @var \SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation $returnAnnotation */
					$returnAnnotation = $returnAnnotation;

					$phpcsFile->addError(
						sprintf(
							'@return annotation of %s %s() does not specify type hint for items of its traversable return value.',
							lcfirst($this->getFunctionTypeLabel($phpcsFile, $functionPointer)),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
						),
						$returnAnnotation->getStartPointer(),
						self::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION
					);
				}
			}
		}

		if ($returnTypeHint !== null) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_RETURN_TYPE_HINT))) {
			return;
		}

		$isAbstract = FunctionHelper::isAbstract($phpcsFile, $functionPointer);

		$returnsValue = $isAbstract ? ($hasReturnAnnotation && !$isAnnotationReturnTypeVoid) : FunctionHelper::returnsValue($phpcsFile, $functionPointer);

		$methodsWithoutVoidSupport = ['__construct' => true, '__destruct' => true, '__clone' => true];

		if (!$hasReturnAnnotation) {
			if ($returnsValue) {
				$phpcsFile->addError(
					sprintf(
						'%s %s() does not have return type hint nor @return annotation for its return value.',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
					),
					$functionPointer,
					self::CODE_MISSING_RETURN_TYPE_HINT
				);
			} elseif (!array_key_exists(FunctionHelper::getName($phpcsFile, $functionPointer), $methodsWithoutVoidSupport)) {
				$fix = $phpcsFile->addFixableError(
					sprintf(
						'%s %s() does not have void return type hint.',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
					),
					$functionPointer,
					self::CODE_MISSING_RETURN_TYPE_HINT
				);

				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContent($phpcsFile->getTokens()[$functionPointer]['parenthesis_closer'], ': void');
					$phpcsFile->fixer->endChangeset();
				}
			}

			return;
		}

		if (
			$returnTypeNode instanceof IdentifierTypeNode
			&& $returnTypeNode->name === 'void'
			&& !$returnsValue
			&& !array_key_exists(FunctionHelper::getName($phpcsFile, $functionPointer), $methodsWithoutVoidSupport)
		) {
			$fix = $phpcsFile->addFixableError(
				sprintf(
					'%s %s() does not have return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
					$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
					FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
					AnnotationTypeHelper::export($returnTypeNode)
				),
				$functionPointer,
				self::CODE_MISSING_RETURN_TYPE_HINT
			);

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($phpcsFile->getTokens()[$functionPointer]['parenthesis_closer'], ': void');
				$phpcsFile->fixer->endChangeset();
			}

			return;
		}

		$annotationContainsOneType = $this->annotationContainsOneType($returnTypeNode);
		if (
			!$annotationContainsOneType
			&& !$this->annotationContainsJustTwoTypes($returnTypeNode)
		) {
			return;
		}

		if ($annotationContainsOneType) {
			/** @var \PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $returnTypeNode */
			$returnTypeNode = $returnTypeNode;
			$possibleReturnTypeHint = $returnTypeNode instanceof ArrayTypeNode ? 'array' : $this->getTypeHintFromOneType($returnTypeNode);
			$nullableReturnTypeHint = false;

		} else {
			/** @var \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode $returnTypeNode */
			$returnTypeNode = $returnTypeNode;

			if (
				!$this->annotationTypeContainsNullType($returnTypeNode)
				&& !$this->annotationContainsTraversableType($phpcsFile, $functionPointer, $returnTypeNode)
			) {
				return;
			}

			if ($this->annotationTypeContainsNullType($returnTypeNode)) {
				/** @var \PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|\PHPStan\PhpDocParser\Ast\Type\GenericTypeNode $notNullTypeHintNode */
				$notNullTypeHintNode = $this->getTypeFromNullableType($returnTypeNode);
				$possibleReturnTypeHint = $notNullTypeHintNode instanceof ArrayTypeNode ? 'array' : $this->getTypeHintFromOneType($notNullTypeHintNode);
				$nullableReturnTypeHint = true;
			} else {
				$itemsSpecificationTypeHint = $this->getItemsSpecificationTypeFromType($returnTypeNode);
				if (!$itemsSpecificationTypeHint instanceof ArrayTypeNode) {
					return;
				}

				$possibleReturnTypeHint = $this->getTraversableTypeHintFromType($returnTypeNode);
				$nullableReturnTypeHint = false;

				if (!$this->isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $possibleReturnTypeHint))) {
					return;
				}
			}
		}

		if (!$this->isValidTypeHint($possibleReturnTypeHint)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'%s %s() does not have return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
				$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
				AnnotationTypeHelper::export($returnTypeNode)
			),
			$functionPointer,
			self::CODE_MISSING_RETURN_TYPE_HINT
		);
		if (!$fix) {
			return;
		}

		$returnTypeHint = TypeHintHelper::isSimpleTypeHint($possibleReturnTypeHint)
			? TypeHintHelper::convertLongSimpleTypeHintToShort($possibleReturnTypeHint)
			: $possibleReturnTypeHint;

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent($phpcsFile->getTokens()[$functionPointer]['parenthesis_closer'], sprintf(': %s%s', ($nullableReturnTypeHint ? '?' : ''), $returnTypeHint));
		$phpcsFile->fixer->endChangeset();
	}

	private function checkClosure(File $phpcsFile, int $closurePointer): void
	{
		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $closurePointer);
		$returnsValue = FunctionHelper::returnsValue($phpcsFile, $closurePointer);

		if (!$returnsValue && $returnTypeHint !== null && $returnTypeHint->getTypeHint() !== 'void') {
			$fix = $phpcsFile->addFixableError(
				'Closure has incorrect return type hint.',
				$closurePointer,
				self::CODE_INCORRECT_RETURN_TYPE_HINT
			);

			if ($fix) {
				$tokens = $phpcsFile->getTokens();
				/** @var int $closeParenthesisPosition */
				$closeParenthesisPosition = TokenHelper::findPrevious($phpcsFile, [T_CLOSE_PARENTHESIS], $tokens[$closurePointer]['scope_opener'] - 1, $closurePointer);

				$phpcsFile->fixer->beginChangeset();
				for ($i = $closeParenthesisPosition + 1; $i < $tokens[$closurePointer]['scope_opener']; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->replaceToken($closeParenthesisPosition, '): void ');
				$phpcsFile->fixer->endChangeset();
			}

			return;
		}

		if ($returnsValue || $returnTypeHint !== null) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Closure does not have void return type hint.',
			$closurePointer,
			self::CODE_MISSING_RETURN_TYPE_HINT
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

	private function checkUselessDocComment(File $phpcsFile, int $functionPointer): void
	{
		$docCommentSniffSuppressed = SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_USELESS_DOC_COMMENT));
		$returnSniffSuppressed = SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_USELESS_RETURN_ANNOTATION));
		$parameterSniffSuppressed = SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_USELESS_PARAMETER_ANNOTATION));

		if ($docCommentSniffSuppressed && $returnSniffSuppressed && $parameterSniffSuppressed) {
			return;
		}

		if ($this->hasInheritdocAnnotation($phpcsFile, $functionPointer)) {
			return;
		}

		if (!DocCommentHelper::hasDocComment($phpcsFile, $functionPointer)) {
			return;
		}

		$containsUsefulInformation = DocCommentHelper::hasDocCommentDescription($phpcsFile, $functionPointer);

		$parametersAnnotationsUseful = [];
		foreach (FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer) as $parameterAnnotation) {
			if ($parameterAnnotation->getContent() === null) {
				continue;
			}

			if ($parameterAnnotation->isInvalid()) {
				continue;
			}

			if (!$parameterAnnotation->hasDescription()) {
				continue;
			}

			$parametersAnnotationsUseful[$parameterAnnotation->getParameterName()] = true;
			$containsUsefulInformation = true;
		}

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
		$isReturnAnnotationUseless = $this->isReturnAnnotationUseless($phpcsFile, $functionPointer, $returnTypeHint, $returnAnnotation);

		$parameterTypeHints = FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer);
		$parametersAnnotationTypeHints = $this->getFunctionParametersAnnotations($phpcsFile, $functionPointer);
		$uselessParameterAnnotations = $this->getUselessParameterAnnotations($phpcsFile, $functionPointer, $parameterTypeHints, $parametersAnnotationTypeHints, $parametersAnnotationsUseful);

		foreach (AnnotationHelper::getAnnotations($phpcsFile, $functionPointer) as [$annotation]) {
			if ($annotation->getName() === SuppressHelper::ANNOTATION) {
				$containsUsefulInformation = true;
				break;
			}

			if ($this->allAnnotationsAreUseful && !in_array($annotation->getName(), ['@param', '@return'], true)) {
				$containsUsefulInformation = true;
				break;
			}
		}

		$isWholeDocCommentUseless = !$containsUsefulInformation
			&& ($returnAnnotation === null || $isReturnAnnotationUseless)
			&& count($uselessParameterAnnotations) === count($parametersAnnotationTypeHints);

		if ($this->enableEachParameterAndReturnInspection && (!$isWholeDocCommentUseless || $docCommentSniffSuppressed)) {
			if ($returnAnnotation !== null && $isReturnAnnotationUseless && !$returnSniffSuppressed) {
				$fix = $phpcsFile->addFixableError(
					sprintf(
						'%s %s() has useless @return annotation.',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
					),
					$returnAnnotation->getStartPointer(),
					self::CODE_USELESS_RETURN_ANNOTATION
				);
				if ($fix) {
					/** @var int $changeStart */
					$changeStart = TokenHelper::findPrevious($phpcsFile, T_DOC_COMMENT_STAR, $returnAnnotation->getStartPointer() - 1);
					/** @var int $changeEnd */
					$changeEnd = TokenHelper::findNext($phpcsFile, [T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STAR], $returnAnnotation->getEndPointer() + 1) - 1;
					$phpcsFile->fixer->beginChangeset();
					for ($i = $changeStart; $i <= $changeEnd; $i++) {
						$phpcsFile->fixer->replaceToken($i, '');
					}
					$phpcsFile->fixer->endChangeset();
				}
			}

			if (!$parameterSniffSuppressed) {
				foreach ($uselessParameterAnnotations as $uselessParameterAnnotation) {
					$fix = $phpcsFile->addFixableError(
						sprintf(
							'%s %s() has useless @param annotation for parameter %s.',
							$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
							$uselessParameterAnnotation->getParameterName()
						),
						$uselessParameterAnnotation->getStartPointer(),
						self::CODE_USELESS_PARAMETER_ANNOTATION
					);
					if (!$fix) {
						continue;
					}

					/** @var int $changeStart */
					$changeStart = TokenHelper::findPrevious($phpcsFile, T_DOC_COMMENT_STAR, $uselessParameterAnnotation->getStartPointer() - 1);
					/** @var int $changeEnd */
					$changeEnd = TokenHelper::findNext($phpcsFile, [T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STAR], $uselessParameterAnnotation->getEndPointer() + 1) - 1;
					$phpcsFile->fixer->beginChangeset();
					for ($i = $changeStart; $i <= $changeEnd; $i++) {
						$phpcsFile->fixer->replaceToken($i, '');
					}
					$phpcsFile->fixer->endChangeset();
				}
			}

			return;
		}

		if (!$isWholeDocCommentUseless || $docCommentSniffSuppressed) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'%s %s() does not need documentation comment.',
				$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
			),
			$functionPointer,
			self::CODE_USELESS_DOC_COMMENT
		);
		if (!$fix) {
			return;
		}

		/** @var int $docCommentOpenPointer */
		$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $functionPointer);
		$docCommentClosePointer = $phpcsFile->getTokens()[$docCommentOpenPointer]['comment_closer'];

		$changeStart = $docCommentOpenPointer;
		/** @var int $changeEnd */
		$changeEnd = TokenHelper::findNextEffective($phpcsFile, $docCommentClosePointer + 1) - 1;

		$phpcsFile->fixer->beginChangeset();
		for ($i = $changeStart; $i <= $changeEnd; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function checkPropertyTypeHint(File $phpcsFile, int $propertyPointer): void
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, self::NAME)) {
			return;
		}

		if ($this->hasInheritdocAnnotation($phpcsFile, $propertyPointer)) {
			return;
		}

		/** @var \SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation[] $varAnnotations */
		$varAnnotations = AnnotationHelper::getAnnotationsByName($phpcsFile, $propertyPointer, '@var');

		if (count($varAnnotations) === 0) {
			if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $this->getSniffName(self::CODE_MISSING_PROPERTY_TYPE_HINT))) {
				return;
			}

			$phpcsFile->addError(
				sprintf(
					'Property %s does not have @var annotation.',
					PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
				),
				$propertyPointer,
				self::CODE_MISSING_PROPERTY_TYPE_HINT
			);

			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION))) {
			return;
		}

		if ($varAnnotations[0]->isInvalid()) {
			return;
		}

		$propertyTypeNode = $varAnnotations[0]->getType();

		if (
			!$this->annotationContainsTraversableType($phpcsFile, $propertyPointer, $propertyTypeNode)
			|| $this->annotationContainsItemsSpecificationForTraversable($phpcsFile, $propertyPointer, $propertyTypeNode)
		) {
			return;
		}

		$phpcsFile->addError(
			sprintf(
				'@var annotation of property %s does not specify type hint for its items.',
				PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
			),
			$varAnnotations[0]->getStartPointer(),
			self::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION
		);
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

	private function isValidTypeHint(string $typeHint): bool
	{
		if (TypeHintHelper::isSimpleTypeHint($typeHint)) {
			return true;
		}

		if ($typeHint === 'object') {
			return $this->enableObjectTypeHint;
		}

		return !TypeHintHelper::isSimpleUnofficialTypeHints($typeHint);
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode $typeNode
	 * @return bool
	 */
	private function annotationTypeContainsNullType(TypeNode $typeNode): bool
	{
		foreach ($typeNode->types as $innerTypeNode) {
			if (!$innerTypeNode instanceof IdentifierTypeNode) {
				continue;
			}

			if (strtolower($innerTypeNode->name) === 'null') {
				return true;
			}
		}

		return false;
	}

	private function annotationContainsStaticOrThisType(TypeNode $typeNode): bool
	{
		if ($typeNode instanceof ThisTypeNode) {
			return true;
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			return strtolower($typeNode->name) === 'static';
		}

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			foreach ($typeNode->types as $innerTypeNode) {
				if ($this->annotationContainsStaticOrThisType($innerTypeNode)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\CallableTypeNode|\PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $typeNode
	 * @return string
	 */
	private function getTypeHintFromOneType(TypeNode $typeNode): string
	{
		if ($typeNode instanceof GenericTypeNode) {
			return $typeNode->type->name;
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			return $typeNode->name;
		}

		if ($typeNode instanceof CallableTypeNode) {
			return $typeNode->identifier->name;
		}

		return (string) $typeNode;
	}

	private function annotationContainsOneType(TypeNode $typeNode): bool
	{
		if ($typeNode instanceof IdentifierTypeNode) {
			return true;
		}

		if ($typeNode instanceof ThisTypeNode) {
			return true;
		}

		if ($typeNode instanceof GenericTypeNode) {
			return true;
		}

		if ($typeNode instanceof CallableTypeNode) {
			return true;
		}

		return $typeNode instanceof ArrayTypeNode;
	}

	private function annotationContainsJustTwoTypes(TypeNode $typeNode): bool
	{
		if (
			!$typeNode instanceof UnionTypeNode
			&& !$typeNode instanceof IntersectionTypeNode
		) {
			return false;
		}

		return count($typeNode->types) === 2;
	}

	private function isAnnotationCompoundOfNull(TypeNode $typeNode): bool
	{
		if (!$this->annotationContainsJustTwoTypes($typeNode)) {
			return false;
		}

		/** @var \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode $typeNode */
		$typeNode = $typeNode;
		return $this->annotationTypeContainsNullType($typeNode);
	}

	private function annotationContainsTraversableType(File $phpcsFile, int $pointer, TypeNode $typeNode): bool
	{
		if ($typeNode instanceof GenericTypeNode) {
			return true;
		}

		if ($typeNode instanceof ArrayTypeNode) {
			return true;
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			$fullyQualifiedType = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $pointer, $typeNode->name);
			return $this->isTraversableType($fullyQualifiedType);
		}

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			foreach ($typeNode->types as $innerTypeNode) {
				if ($this->annotationContainsTraversableType($phpcsFile, $pointer, $innerTypeNode)) {
					return true;
				}
			}
		}

		return false;
	}

	private function annotationContainsItemsSpecificationForTraversable(File $phpcsFile, int $pointer, TypeNode $typeNode, bool $inTraversable = false): bool
	{
		if ($typeNode instanceof GenericTypeNode) {
			foreach ($typeNode->genericTypes as $genericType) {
				if (!$this->annotationContainsItemsSpecificationForTraversable($phpcsFile, $pointer, $genericType, true)) {
					return false;
				}
			}

			return true;
		}

		if ($typeNode instanceof IdentifierTypeNode) {
			if (!$inTraversable) {
				return false;
			}

			return !$this->isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $pointer, $typeNode->name));
		}

		if ($typeNode instanceof ArrayTypeNode) {
			return $this->annotationContainsItemsSpecificationForTraversable($phpcsFile, $pointer, $typeNode->type, true);
		}

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			foreach ($typeNode->types as $innerTypeNode) {
				if (
					!$inTraversable
					&& $innerTypeNode instanceof IdentifierTypeNode
					&& strtolower($innerTypeNode->name) === 'null'
				) {
					continue;
				}

				if ($this->annotationContainsItemsSpecificationForTraversable($phpcsFile, $pointer, $innerTypeNode, $inTraversable)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode $typeNode
	 * @return \PHPStan\PhpDocParser\Ast\Type\TypeNode
	 */
	private function getTypeFromNullableType(TypeNode $typeNode): TypeNode
	{
		return $typeNode->types[0] instanceof IdentifierTypeNode && strtolower($typeNode->types[0]->name) === 'null' ? $typeNode->types[1] : $typeNode->types[0];
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode $typeNode
	 * @return string
	 */
	private function getTraversableTypeHintFromType(TypeNode $typeNode): string
	{
		if ($this->annotationContainsOneType($typeNode->types[0])) {
			/** @var \PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode $oneTypeNode */
			$oneTypeNode = $typeNode->types[0];
			$typeHint = $this->getTypeHintFromOneType($oneTypeNode);
			if ($this->isTraversableType($typeHint)) {
				return $typeHint;
			}
		}

		/** @var \PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode $oneTypeNode */
		$oneTypeNode = $typeNode->types[1];
		return $this->getTypeHintFromOneType($oneTypeNode);
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode $typeNode
	 * @return \PHPStan\PhpDocParser\Ast\Type\TypeNode
	 */
	private function getItemsSpecificationTypeFromType(TypeNode $typeNode): TypeNode
	{
		if ($this->annotationContainsOneType($typeNode->types[0])) {
			/** @var \PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode $oneTypeNode */
			$oneTypeNode = $typeNode->types[0];
			$typeHint = $this->getTypeHintFromOneType($oneTypeNode);
			if ($this->isTraversableType($typeHint)) {
				return $typeNode->types[1];
			}
		}

		return $typeNode->types[0];
	}

	private function isTraversableType(string $type): bool
	{
		return TypeHintHelper::isSimpleIterableTypeHint($type) || array_key_exists($type, $this->getNormalizedTraversableTypeHints());
	}

	/**
	 * @return array<string, int>
	 */
	private function getNormalizedTraversableTypeHints(): array
	{
		if ($this->normalizedTraversableTypeHints === null) {
			$this->normalizedTraversableTypeHints = array_flip(array_map(function (string $typeHint): string {
				return NamespaceHelper::isFullyQualifiedName($typeHint) ? $typeHint : sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}, SniffSettingsHelper::normalizeArray($this->traversableTypeHints)));
		}
		return $this->normalizedTraversableTypeHints;
	}

	private function getFunctionTypeLabel(File $phpcsFile, int $functionPointer): string
	{
		return FunctionHelper::isMethod($phpcsFile, $functionPointer) ? 'Method' : 'Function';
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 * @return \SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation[]
	 */
	private function getFunctionParametersAnnotations(File $phpcsFile, int $functionPointer): array
	{
		$parametersAnnotations = [];
		foreach (FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer) as $parameterAnnotation) {
			if ($parameterAnnotation->getContent() === null) {
				continue;
			}

			if ($parameterAnnotation->isInvalid()) {
				continue;
			}

			$parametersAnnotations[$parameterAnnotation->getParameterName()] = $parameterAnnotation;
		}

		return $parametersAnnotations;
	}

	private function typeHintEqualsAnnotation(File $phpcsFile, int $functionPointer, string $typeHint, string $typeHintInAnnotation): bool
	{
		return TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHint) === TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHintInAnnotation);
	}

	private function isReturnAnnotationUseless(File $phpcsFile, int $functionPointer, ?ReturnTypeHint $returnTypeHint, ?ReturnAnnotation $returnAnnotation): bool
	{
		if ($returnTypeHint === null || $returnAnnotation === null || $returnAnnotation->getContent() === null) {
			return false;
		}

		if ($returnAnnotation->isInvalid()) {
			return false;
		}

		if ($returnAnnotation->hasDescription()) {
			return false;
		}

		if ($this->isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint()))) {
			return false;
		}

		if ($this->annotationContainsStaticOrThisType($returnAnnotation->getType())) {
			return false;
		}

		if ($this->isAnnotationCompoundOfNull($returnAnnotation->getType())) {
			/** @var \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode $returnAnnotationTypeNode */
			$returnAnnotationTypeNode = $returnAnnotation->getType();

			$annotationTypeHintNode = $this->getTypeFromNullableType($returnAnnotationTypeNode);
			$annotationTypeHint = $annotationTypeHintNode instanceof IdentifierTypeNode ? $annotationTypeHintNode->name : (string) $annotationTypeHintNode;
			return $this->typeHintEqualsAnnotation($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint(), $annotationTypeHint);
		}

		if (!$this->annotationContainsOneType($returnAnnotation->getType())) {
			return false;
		}

		if ($returnAnnotation->getType() instanceof CallableTypeNode) {
			return false;
		}

		/** @var \PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\CallableTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $returnAnnotationTypeNode */
		$returnAnnotationTypeNode = $returnAnnotation->getType();
		$returnAnnotationTypeHint = $this->getTypeHintFromOneType($returnAnnotationTypeNode);
		return $this->typeHintEqualsAnnotation($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint(), $returnAnnotationTypeHint);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 * @param (\SlevomatCodingStandard\Helpers\ParameterTypeHint|null)[] $functionTypeHints
	 * @param \SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation[] $parametersAnnotations
	 * @param bool[] $parametersAnnotationsUseful
	 * @return \SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation[]
	 */
	private function getUselessParameterAnnotations(File $phpcsFile, int $functionPointer, array $functionTypeHints, array $parametersAnnotations, array $parametersAnnotationsUseful): array
	{
		$uselessParameterAnnotations = [];

		foreach ($functionTypeHints as $parameterName => $parameterTypeHint) {
			if ($parameterTypeHint === null) {
				continue;
			}

			if (!array_key_exists($parameterName, $parametersAnnotations)) {
				continue;
			}

			if (array_key_exists($parameterName, $parametersAnnotationsUseful)) {
				continue;
			}

			if ($this->isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint()))) {
				continue;
			}

			$parameterAnnotationTypeNode = $parametersAnnotations[$parameterName]->getType();

			if ($this->annotationContainsStaticOrThisType($parameterAnnotationTypeNode)) {
				continue;
			}

			if ($this->isAnnotationCompoundOfNull($parameterAnnotationTypeNode)) {
				/** @var \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode $parameterAnnotationTypeNode */
				$parameterAnnotationTypeNode = $parameterAnnotationTypeNode;

				/** @var \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $annotationTypeNode */
				$annotationTypeNode = $this->getTypeFromNullableType($parameterAnnotationTypeNode);
				$annotationTypeHint = $annotationTypeNode instanceof IdentifierTypeNode ? $annotationTypeNode->name : (string) $annotationTypeNode;
				if (!$this->typeHintEqualsAnnotation($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint(), $annotationTypeHint)) {
					continue;
				}
			} else {
				if (!$this->annotationContainsOneType($parameterAnnotationTypeNode)) {
					continue;
				}

				if ($parameterAnnotationTypeNode instanceof CallableTypeNode) {
					continue;
				}

				/** @var \PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\CallableTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $parameterAnnotationTypeNode */
				$parameterAnnotationTypeNode = $parameterAnnotationTypeNode;
				$parameterAnnotationTypeHint = $this->getTypeHintFromOneType($parameterAnnotationTypeNode);
				if (!$this->typeHintEqualsAnnotation($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint(), $parameterAnnotationTypeHint)) {
					continue;
				}
			}

			$uselessParameterAnnotations[] = $parametersAnnotations[$parameterName];
		}

		return $uselessParameterAnnotations;
	}

	private function hasInheritdocAnnotation(File $phpcsFile, int $pointer): bool
	{
		$docComment = DocCommentHelper::getDocComment($phpcsFile, $pointer);
		if ($docComment === null) {
			return false;
		}

		return stripos($docComment, '@inheritdoc') !== false;
	}

}
