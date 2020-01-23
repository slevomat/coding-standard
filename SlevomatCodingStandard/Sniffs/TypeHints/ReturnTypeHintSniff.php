<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
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
use SlevomatCodingStandard\Helpers\ReturnTypeHint;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use function array_key_exists;
use function array_map;
use function array_unique;
use function array_values;
use function count;
use function lcfirst;
use function sprintf;
use function strtolower;
use const PHP_VERSION_ID;
use const T_CLOSURE;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_FUNCTION;

class ReturnTypeHintSniff implements Sniff
{

	public const CODE_MISSING_ANY_TYPE_HINT = 'MissingAnyTypeHint';

	public const CODE_MISSING_NATIVE_TYPE_HINT = 'MissingNativeTypeHint';

	public const CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION = 'MissingTraversableTypeHintSpecification';

	public const CODE_USELESS_ANNOTATION = 'UselessAnnotation';

	private const NAME = 'SlevomatCodingStandard.TypeHints.ReturnTypeHint';

	/** @var bool */
	public $enableObjectTypeHint = PHP_VERSION_ID >= 70200;

	/** @var string[] */
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
	 * @param File $phpcsFile
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
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

			$this->checkFunctionTypeHint($phpcsFile, $pointer, $returnTypeHint, $returnAnnotation);
			$this->checkFunctionTraversableTypeHintSpecification($phpcsFile, $pointer, $returnTypeHint, $returnAnnotation);
			$this->checkFunctionUselessAnnotation($phpcsFile, $pointer, $returnTypeHint, $returnAnnotation);
		} elseif ($token['code'] === T_CLOSURE) {
			$this->checkClosureTypeHint($phpcsFile, $pointer);
		}
	}

	private function checkFunctionTypeHint(
		File $phpcsFile,
		int $functionPointer,
		?ReturnTypeHint $returnTypeHint,
		?ReturnAnnotation $returnAnnotation
	): void
	{
		if ($returnTypeHint !== null) {
			return;
		}

		$methodsWithoutVoidSupport = ['__construct' => true, '__destruct' => true, '__clone' => true];

		if (array_key_exists(FunctionHelper::getName($phpcsFile, $functionPointer), $methodsWithoutVoidSupport)) {
			return;
		}

		$hasReturnAnnotation = $this->hasReturnAnnotation($returnAnnotation);
		$returnTypeNode = $this->getReturnTypeNode($returnAnnotation);
		$isAnnotationReturnTypeVoid = $returnTypeNode instanceof IdentifierTypeNode && strtolower($returnTypeNode->name) === 'void';
		$isAbstract = FunctionHelper::isAbstract($phpcsFile, $functionPointer);
		$returnsValue = $isAbstract ? ($hasReturnAnnotation && !$isAnnotationReturnTypeVoid) : FunctionHelper::returnsValue($phpcsFile, $functionPointer);

		if ($returnsValue && !$hasReturnAnnotation) {
			if (!SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, self::getSniffName(self::CODE_MISSING_ANY_TYPE_HINT))) {
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

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, self::getSniffName(self::CODE_MISSING_NATIVE_TYPE_HINT))) {
			return;
		}

		if (
			!$returnsValue
			&& (!$hasReturnAnnotation || $isAnnotationReturnTypeVoid)
		) {
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
					AnnotationTypeHelper::export($returnTypeNode)
				);

			$fix = $phpcsFile->addFixableError($message, $functionPointer, self::getSniffName(self::CODE_MISSING_NATIVE_TYPE_HINT));

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($phpcsFile->getTokens()[$functionPointer]['parenthesis_closer'], ': void');
				$phpcsFile->fixer->endChangeset();
			}

			return;
		}

		if (AnnotationTypeHelper::containsOneType($returnTypeNode)) {
			/** @var ArrayTypeNode|ArrayShapeNode|GenericTypeNode|IdentifierTypeNode|ThisTypeNode $returnTypeNode */
			$returnTypeNode = $returnTypeNode;
			$possibleReturnTypeHint = $returnTypeNode instanceof ArrayTypeNode || $returnTypeNode instanceof ArrayShapeNode
				? 'array'
				: AnnotationTypeHelper::getTypeHintFromOneType($returnTypeNode);
			$nullableReturnTypeHint = false;

		} else {
			$possibleReturnTypeHint = null;
			$nullableReturnTypeHint = false;

			if ($returnTypeNode instanceof UnionTypeNode && !AnnotationTypeHelper::containsJustTwoTypes($returnTypeNode)) {
				$typeHints = [];
				foreach ($returnTypeNode->types as $typeNode) {
					if (!($typeNode instanceof CallableTypeNode
						|| $typeNode instanceof GenericTypeNode
						|| $typeNode instanceof IdentifierTypeNode
						|| $typeNode instanceof ThisTypeNode)
					) {
						return;
					}

					$typeHints[] = AnnotationTypeHelper::getTypeHintFromOneType($typeNode);
				}

				$typeHints = array_values(array_unique($typeHints));

				if (count($typeHints) === 1) {
					$possibleReturnTypeHint = $typeHints[0];
					$nullableReturnTypeHint = false;
				} elseif (count($typeHints) === 2 && ($typeHints[0] === 'null' || $typeHints[1] === 'null')) {
					$possibleReturnTypeHint = $typeHints[0] === 'null' ? $typeHints[1] : $typeHints[0];
					$nullableReturnTypeHint = true;
				} else {
					return;
				}
			}

			if ($possibleReturnTypeHint === null) {
				/** @var UnionTypeNode|IntersectionTypeNode $returnTypeNode */
				$returnTypeNode = $returnTypeNode;

				if (
					!AnnotationTypeHelper::containsNullType($returnTypeNode)
					&& !AnnotationTypeHelper::containsTraversableType($returnTypeNode, $phpcsFile, $functionPointer, $this->getTraversableTypeHints())
				) {
					return;
				}

				if (AnnotationTypeHelper::containsNullType($returnTypeNode)) {
					/** @var ArrayTypeNode|ArrayShapeNode|IdentifierTypeNode|ThisTypeNode|GenericTypeNode $notNullTypeHintNode */
					$notNullTypeHintNode = AnnotationTypeHelper::getTypeFromNullableType($returnTypeNode);
					$possibleReturnTypeHint = $notNullTypeHintNode instanceof ArrayTypeNode || $notNullTypeHintNode instanceof ArrayShapeNode
						? 'array'
						: AnnotationTypeHelper::getTypeHintFromOneType($notNullTypeHintNode);
					$nullableReturnTypeHint = true;
				} else {
					$itemsSpecificationTypeHint = AnnotationTypeHelper::getItemsSpecificationTypeFromType($returnTypeNode, $this->getTraversableTypeHints());
					if (!$itemsSpecificationTypeHint instanceof ArrayTypeNode) {
						return;
					}

					$possibleReturnTypeHint = AnnotationTypeHelper::getTraversableTypeHintFromType($returnTypeNode, $this->getTraversableTypeHints());
					$nullableReturnTypeHint = false;

					if (!TypeHintHelper::isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $possibleReturnTypeHint), $this->getTraversableTypeHints())) {
						return;
					}
				}
			}
		}

		if (!TypeHintHelper::isValidTypeHint($possibleReturnTypeHint, $this->enableObjectTypeHint)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'%s %s() does not have native return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
				FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
				AnnotationTypeHelper::export($returnTypeNode)
			),
			$functionPointer,
			self::CODE_MISSING_NATIVE_TYPE_HINT
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

	private function checkFunctionTraversableTypeHintSpecification(
		File $phpcsFile,
		int $functionPointer,
		?ReturnTypeHint $returnTypeHint,
		?ReturnAnnotation $returnAnnotation
	): void
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, self::getSniffName(self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION))) {
			return;
		}

		$hasTraversableTypeHint = $this->hasTraversableTypeHint($phpcsFile, $functionPointer, $returnTypeHint, $returnAnnotation);
		$hasReturnAnnotation = $this->hasReturnAnnotation($returnAnnotation);

		if ($hasTraversableTypeHint && !$hasReturnAnnotation) {
			$phpcsFile->addError(
				sprintf(
					'%s %s() does not have @return annotation for its traversable return value.',
					FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
					FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
				),
				$functionPointer,
				self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION
			);

			return;
		}

		$returnTypeNode = $this->getReturnTypeNode($returnAnnotation);

		if (!$hasReturnAnnotation) {
			return;
		}

		if (!$hasTraversableTypeHint && !AnnotationTypeHelper::containsTraversableType($returnTypeNode, $phpcsFile, $functionPointer, $this->getTraversableTypeHints())) {
			return;
		}

		if (AnnotationTypeHelper::containsItemsSpecificationForTraversable($returnTypeNode, $phpcsFile, $functionPointer, $this->getTraversableTypeHints())) {
			return;
		}

		/** @var ReturnAnnotation $returnAnnotation */
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
		?ReturnTypeHint $returnTypeHint,
		?ReturnAnnotation $returnAnnotation
	): void
	{
		if ($returnAnnotation === null) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, self::getSniffName(self::CODE_USELESS_ANNOTATION))) {
			return;
		}

		if (!AnnotationHelper::isAnnotationUseless($phpcsFile, $functionPointer, $returnTypeHint, $returnAnnotation, $this->getTraversableTypeHints())) {
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
	 * @param ReturnAnnotation|null $returnAnnotation
	 * @return GenericTypeNode|CallableTypeNode|IntersectionTypeNode|UnionTypeNode|ArrayTypeNode|ArrayShapeNode|IdentifierTypeNode|ThisTypeNode|null
	 */
	private function getReturnTypeNode(?ReturnAnnotation $returnAnnotation): ?TypeNode
	{
		if ($this->hasReturnAnnotation($returnAnnotation)) {
			return $returnAnnotation->getType();
		}

		return null;
	}

	private function hasTraversableTypeHint(
		File $phpcsFile,
		int $functionPointer,
		?ReturnTypeHint $returnTypeHint,
		?ReturnAnnotation $returnAnnotation
	): bool
	{
		if ($returnTypeHint !== null && TypeHintHelper::isTraversableType(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint()), $this->getTraversableTypeHints())) {
			return true;
		}

		return
			$this->hasReturnAnnotation($returnAnnotation)
			&& AnnotationTypeHelper::containsTraversableType($this->getReturnTypeNode($returnAnnotation), $phpcsFile, $functionPointer, $this->getTraversableTypeHints());
	}

	private function hasReturnAnnotation(?ReturnAnnotation $returnAnnotation): bool
	{
		return $returnAnnotation !== null && $returnAnnotation->getContent() !== null && !$returnAnnotation->isInvalid();
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

}
