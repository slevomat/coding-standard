<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\ReturnTypeHint;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use const PHP_VERSION_ID;
use const T_BITWISE_AND;
use const T_CLOSE_PARENTHESIS;
use const T_CLOSURE;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use const T_ELLIPSIS;
use const T_EQUAL;
use const T_FUNCTION;
use const T_VARIABLE;
use function array_flip;
use function array_key_exists;
use function array_map;
use function array_reduce;
use function count;
use function explode;
use function in_array;
use function lcfirst;
use function preg_match;
use function preg_match_all;
use function preg_split;
use function sprintf;
use function stripos;
use function strpos;
use function strtolower;
use function substr;

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
	public $enableNullableTypeHints = true;

	/**
	 * @deprecated
	 * @var bool
	 */
	public $enableVoidTypeHint = true;

	/** @var bool */
	public $enableObjectTypeHint = PHP_VERSION_ID >= 70200;

	/** @var bool */
	public $enableEachParameterAndReturnInspection = false;

	/** @var string[] */
	public $traversableTypeHints = [];

	/** @var string[] */
	public $usefulAnnotations = [];

	/** @var bool */
	public $allAnnotationsAreUseful = false;

	/** @var int[]|null [string => int] */
	private $normalizedTraversableTypeHints;

	/** @var string[]|null */
	private $normalizedUsefulAnnotations;

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
	 * @return mixed[]
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

		$parametersTypeHintsDefinitions = $this->getFunctionParameterTypeHintsDefinitions($phpcsFile, $functionPointer);

		if (!SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION))) {
			foreach (FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer) as $parameterName => $parameterTypeHint) {
				$traversableTypeHint = false;
				if ($parameterTypeHint !== null) {
                    $traversableTypeHint = $this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $this->parseType($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint()));
				} elseif (array_key_exists($parameterName, $parametersTypeHintsDefinitions) && $this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $parametersTypeHintsDefinitions[$parameterName]['definition'])) {
					$traversableTypeHint = true;
				}

				if ($traversableTypeHint && !array_key_exists($parameterName, $parametersTypeHintsDefinitions)) {
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
				} elseif (($traversableTypeHint && !$this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $functionPointer, $parametersTypeHintsDefinitions[$parameterName]['definition']))
					|| (
						array_key_exists($parameterName, $parametersTypeHintsDefinitions)
						&& $this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $functionPointer, $parametersTypeHintsDefinitions[$parameterName]['definition'])
						&& !$this->definitionContainsItemsSpecificationForTraversable($phpcsFile, $functionPointer, $parametersTypeHintsDefinitions[$parameterName]['definition'])
					)
				) {
					$phpcsFile->addError(
						sprintf(
							'@param annotation of %s %s() does not specify type hint for items of its traversable parameter %s.',
							lcfirst($this->getFunctionTypeLabel($phpcsFile, $functionPointer)),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
							$parameterName
						),
						$parametersTypeHintsDefinitions[$parameterName]['pointer'],
						self::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION
					);
				}
			}
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_PARAMETER_TYPE_HINT))) {
			return;
		}

		foreach (FunctionHelper::getParametersWithoutTypeHint($phpcsFile, $functionPointer) as $parameterName) {
			if (!isset($parametersTypeHintsDefinitions[$parameterName])) {
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

			$parameterTypeHintDefinition = $parametersTypeHintsDefinitions[$parameterName]['definition'];

			if ($parameterTypeHintDefinition instanceof IdentifierTypeNode && $parameterTypeHintDefinition->name === 'null') {
				continue;
			}

			if ($this->definitionContainsOneTypeHint($parameterTypeHintDefinition)) {
				if ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $parameterTypeHintDefinition)) {
					$phpcsFile->addError(
						sprintf(
							'%s %s() does not have parameter type hint for its parameter %s but it should be possible to add it based on @param annotation "%s".',
							$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
							$parameterName,
							$parameterTypeHintDefinition
						),
						$functionPointer,
						self::CODE_MISSING_PARAMETER_TYPE_HINT
					);
					return;
				} elseif ($this->isValidTypeHint((string) $parameterTypeHintDefinition)) {
					$possibleParameterTypeHint = $parameterTypeHintDefinition;
					$nullableParameterTypeHint = false;
				} else {
					return;
				}
			} elseif ($this->definitionContainsJustTwoTypeHints($parameterTypeHintDefinition)) {
				if ($this->definitionContainsNullTypeHint($parameterTypeHintDefinition)) {
					$parameterTypeHintDefinitionParts = $parameterTypeHintDefinition->types;
					if($parameterTypeHintDefinitionParts[0] instanceof IdentifierTypeNode && $parameterTypeHintDefinitionParts[0]->name === 'null') {
						$possibleParameterTypeHint = $parameterTypeHintDefinitionParts[1];
					} else {
						$possibleParameterTypeHint = $parameterTypeHintDefinitionParts[0];
					}
					$nullableParameterTypeHint = true;
					if ($this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $functionPointer, $possibleParameterTypeHint)) {
						$phpcsFile->addError(
							sprintf(
								'%s %s() does not have parameter type hint for its parameter %s but it should be possible to add it based on @param annotation "%s".',
								$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
								FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
								$parameterName,
								$parameterTypeHintDefinition
							),
							$functionPointer,
							self::CODE_MISSING_PARAMETER_TYPE_HINT
						);
						return;
					}

					if (!$this->isValidTypeHint((string) $possibleParameterTypeHint)) {
						return;
					}
				} elseif ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $parameterTypeHintDefinition)) {
					$parameterTypeHintDefinitionParts = $parameterTypeHintDefinition->types;
					$nullableParameterTypeHint = false;

					if ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $parameterTypeHintDefinitionParts[0])) {
						$possibleParameterTypeHint = $parameterTypeHintDefinitionParts[0];
						$itemsTypeHintDefinition = $parameterTypeHintDefinitionParts[1];
					} else {
						$possibleParameterTypeHint = $parameterTypeHintDefinitionParts[1];
						$itemsTypeHintDefinition = $parameterTypeHintDefinitionParts[0];
					}

					if (!$this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $functionPointer, $itemsTypeHintDefinition)) {
						return;
					}

				} else {
					return;
				}
			} else {
				return;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf(
					'%s %s() does not have parameter type hint for its parameter %s but it should be possible to add it based on @param annotation "%s".',
					$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
					FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
					$parameterName,
					$parameterTypeHintDefinition
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

			if ($this->enableNullableTypeHints) {
				$phpcsFile->fixer->addContentBefore($beforeParameterPointer, sprintf('%s%s ', ($nullableParameterTypeHint ? '?' : ''), $parameterTypeHint));
			} else {
				$phpcsFile->fixer->addContentBefore($beforeParameterPointer, sprintf('%s ', $parameterTypeHint));
				if ($nullableParameterTypeHint && $tokens[TokenHelper::findNextEffective($phpcsFile, $parameterPointer + 1)]['code'] !== T_EQUAL) {
					$phpcsFile->fixer->addContent($parameterPointer, ' = null');
				}
			}

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

		if ($returnAnnotation !== null && $returnAnnotation->getContent() !== null) {
			$hasReturnAnnotation = true;
			$returnTypeHintDefinition = $this->parseType($phpcsFile, $functionPointer, $returnAnnotation->getContent());
		} else {
			$hasReturnAnnotation = false;
			$returnTypeHintDefinition = null;
		}

		$traversableTypeHint = false;
		if ($returnTypeHint !== null && $this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $this->parseType($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint()))) {
			$traversableTypeHint = true;
		} elseif ($hasReturnAnnotation && $this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $returnTypeHintDefinition)) {
			$traversableTypeHint = true;
		}

		if (!SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION))) {
			if ($traversableTypeHint && !$hasReturnAnnotation) {
				$phpcsFile->addError(
					sprintf(
						'%s %s() does not have @return annotation for its traversable return value.',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
					),
					$functionPointer,
					self::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION
				);
			} elseif (($traversableTypeHint && $baz = !$this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $functionPointer, $returnTypeHintDefinition))
				|| (
				    $returnTypeHintDefinition
					&& $foo = $this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $functionPointer, $returnTypeHintDefinition)
					&& $bar = !$this->definitionContainsItemsSpecificationForTraversable($phpcsFile, $functionPointer, $returnTypeHintDefinition)
				)
			) {
				/** @var \SlevomatCodingStandard\Helpers\Annotation $returnAnnotation */
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

		if ($returnTypeHint !== null || SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_RETURN_TYPE_HINT))) {
			return;
		}

		$isAbstract = FunctionHelper::isAbstract($phpcsFile, $functionPointer);

		$returnsValue = $isAbstract ? ($hasReturnAnnotation && $returnTypeHintDefinition !== 'void') : FunctionHelper::returnsValue($phpcsFile, $functionPointer);

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
			} elseif ($this->enableVoidTypeHint && !array_key_exists(FunctionHelper::getName($phpcsFile, $functionPointer), $methodsWithoutVoidSupport)) {
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

		if ($this->enableVoidTypeHint && $returnTypeHintDefinition === 'void' && !$returnsValue && !array_key_exists(FunctionHelper::getName($phpcsFile, $functionPointer), $methodsWithoutVoidSupport)) {
			$fix = $phpcsFile->addFixableError(
				sprintf(
					'%s %s() does not have return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
					$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
					FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
					$returnTypeHintDefinition
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

		if (!$returnsValue) {
			return;
		}

		if ($this->definitionContainsOneTypeHint($returnTypeHintDefinition)) {
			if ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $returnTypeHintDefinition)) {
				$phpcsFile->addError(
					sprintf(
						'%s %s() does not have return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
						$returnTypeHintDefinition
					),
					$functionPointer,
					self::CODE_MISSING_RETURN_TYPE_HINT
				);
				return;
			} elseif ($this->isValidTypeHint($returnAnnotation->getContent())) {
				$possibleReturnTypeHint = $returnTypeHintDefinition;
				$nullableReturnTypeHint = false;
			} else {
				return;
			}
		} elseif ($this->definitionContainsJustTwoTypeHints($returnTypeHintDefinition)) {
			if ($this->definitionContainsNullTypeHint($returnTypeHintDefinition)) {
				if (!$this->enableNullableTypeHints) {
					return;
				}

				$returnTypeHintDefinitionParts = explode('|', $returnTypeHintDefinition);
				$possibleReturnTypeHint = strtolower($returnTypeHintDefinitionParts[0]) === 'null' ? $returnTypeHintDefinitionParts[1] : $returnTypeHintDefinitionParts[0];
				$nullableReturnTypeHint = true;

				if ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $possibleReturnTypeHint)) {
					$phpcsFile->addError(
						sprintf(
							'%s %s() does not have return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
							$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
							$returnTypeHintDefinition
						),
						$functionPointer,
						self::CODE_MISSING_RETURN_TYPE_HINT
					);
					return;
				}

				if (!$this->isValidTypeHint($possibleReturnTypeHint)) {
					return;
				}
			} elseif ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $returnTypeHintDefinition)) {
				$returnTypeHintDefinitionParts = $returnTypeHintDefinition->types;
				$nullableReturnTypeHint = false;

				if ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $returnTypeHintDefinitionParts[0])) {
					$possibleReturnTypeHint = $returnTypeHintDefinitionParts[0];
					$itemsTypeHintDefinition = $returnTypeHintDefinitionParts[1];
				} else {
					$possibleReturnTypeHint = $returnTypeHintDefinitionParts[1];
					$itemsTypeHintDefinition = $returnTypeHintDefinitionParts[0];
				}

				if (!$this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $functionPointer, $itemsTypeHintDefinition)) {
					return;
				}

			} else {
				return;
			}
		} else {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'%s %s() does not have return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
				$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
				$returnTypeHintDefinition
			),
			$functionPointer,
			self::CODE_MISSING_RETURN_TYPE_HINT
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$returnTypeHint = TypeHintHelper::isSimpleTypeHint($possibleReturnTypeHint)
			? TypeHintHelper::convertLongSimpleTypeHintToShort($possibleReturnTypeHint)
			: $possibleReturnTypeHint;
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
				$phpcsFile->fixer->replaceToken($closeParenthesisPosition, $this->enableVoidTypeHint ? '): void ' : ') ');
				$phpcsFile->fixer->endChangeset();
			}

			return;
		}

		if (!$this->enableVoidTypeHint || $returnsValue || $returnTypeHint !== null) {
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

		$parametersNames = FunctionHelper::getParametersNames($phpcsFile, $functionPointer);
		$parametersContainDescription = [];
		foreach (FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer) as $parameterAnnotationNo => $parameterAnnotation) {
			if ($parameterAnnotation->getContent() === null) {
				continue;
			}

			if (!preg_match('~^\\S+\\s++(?:(?:\.{3}\\s*)?(\$\\S+)\\s+)?[^$]~', $parameterAnnotation->getContent(), $matches)) {
				continue;
			}

			if (isset($matches[1])) {
				$parametersContainDescription[$matches[1]] = true;
				$containsUsefulInformation = true;
			} elseif (isset($parametersNames[$parameterAnnotationNo])) {
				$parametersContainDescription[$parametersNames[$parameterAnnotationNo]] = true;
				$containsUsefulInformation = true;
			}
		}

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
		$isReturnAnnotationUseless = $this->isReturnAnnotationUseless($phpcsFile, $functionPointer, $returnTypeHint, $returnAnnotation);

		$parameterTypeHints = FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer);
		$parametersAnnotationTypeHints = $this->getFunctionParameterTypeHintsDefinitions($phpcsFile, $functionPointer);
		$uselessParameterAnnotations = $this->getUselessParameterAnnotations($phpcsFile, $functionPointer, $parameterTypeHints, $parametersAnnotationTypeHints, $parametersContainDescription);

		foreach (AnnotationHelper::getAnnotations($phpcsFile, $functionPointer) as [$annotation]) {
			if ($annotation->getName() === SuppressHelper::ANNOTATION) {
				$containsUsefulInformation = true;
				break;
			}

			if ($this->allAnnotationsAreUseful && !in_array($annotation->getName(), ['@param', '@return'], true)) {
				$containsUsefulInformation = true;
				break;
			}

			foreach ($this->getNormalizedUsefulAnnotations() as $usefulAnnotation) {
				if ($annotation->getName() === $usefulAnnotation) {
					$containsUsefulInformation = true;
					break;
				}

				if (substr($usefulAnnotation, -1) !== '\\' || strpos($annotation->getName(), $usefulAnnotation) !== 0) {
					continue;
				}

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
					$tokens = $phpcsFile->getTokens();
					$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $functionPointer);
					$docCommentClosePointer = $tokens[$docCommentOpenPointer]['comment_closer'];

					for ($i = $docCommentOpenPointer + 1; $i < $docCommentClosePointer; $i++) {
						if ($tokens[$i]['code'] !== T_DOC_COMMENT_TAG) {
							continue;
						}

						if ($tokens[$i]['content'] !== '@return') {
							continue;
						}

						/** @var int $changeStart */
						$changeStart = TokenHelper::findPrevious($phpcsFile, [T_DOC_COMMENT_STAR], $i - 1, $docCommentOpenPointer);
						/** @var int $changeEnd */
						$changeEnd = TokenHelper::findNext($phpcsFile, [T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STAR], $i - 1, $docCommentClosePointer + 1) - 1;
						$phpcsFile->fixer->beginChangeset();
						for ($j = $changeStart; $j <= $changeEnd; $j++) {
							$phpcsFile->fixer->replaceToken($j, '');
						}
						$phpcsFile->fixer->endChangeset();

						break;
					}
				}
			}

			if (!$parameterSniffSuppressed) {
				$parameterNamesWithUselessAnnotation = array_map(function (array $uselessParameterAnnotation): string {
					return $uselessParameterAnnotation['parameterName'];
				}, $uselessParameterAnnotations);

				foreach ($uselessParameterAnnotations as $uselessParameterAnnotation) {
					$fix = $phpcsFile->addFixableError(
						sprintf(
							'%s %s() has useless @param annotation for parameter %s.',
							$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
							$uselessParameterAnnotation['parameterName']
						),
						$uselessParameterAnnotation['pointer'],
						self::CODE_USELESS_PARAMETER_ANNOTATION
					);
					if (!$fix) {
						continue;
					}

					$tokens = $phpcsFile->getTokens();
					$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $functionPointer);
					$docCommentClosePointer = $tokens[$docCommentOpenPointer]['comment_closer'];

					for ($i = $docCommentOpenPointer + 1; $i < $docCommentClosePointer; $i++) {
						if ($tokens[$i]['code'] !== T_DOC_COMMENT_TAG) {
							continue;
						}

						if ($tokens[$i]['content'] !== '@param') {
							continue;
						}

						$parameterInformationPointer = TokenHelper::findNextExcluding($phpcsFile, [T_DOC_COMMENT_WHITESPACE], $i + 1, $docCommentClosePointer + 1);

						if ($parameterInformationPointer === null || $tokens[$parameterInformationPointer]['code'] !== T_DOC_COMMENT_STRING) {
							continue;
						}

						if (!preg_match('~\S+\s+(\$\S+)~', $tokens[$parameterInformationPointer]['content'], $match)) {
							continue;
						}

						if (!in_array($match[1], $parameterNamesWithUselessAnnotation, true)) {
							continue;
						}

						/** @var int $changeStart */
						$changeStart = TokenHelper::findPrevious($phpcsFile, [T_DOC_COMMENT_STAR], $i - 1);
						/** @var int $changeEnd */
						$changeEnd = TokenHelper::findNext($phpcsFile, [T_DOC_COMMENT_CLOSE_TAG, T_DOC_COMMENT_STAR], $i - 1) - 1;
						$phpcsFile->fixer->beginChangeset();
						for ($j = $changeStart; $j <= $changeEnd; $j++) {
							$phpcsFile->fixer->replaceToken($j, '');
						}
						$phpcsFile->fixer->endChangeset();

						break;
					}
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
		} else {
			if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION))) {
				return;
			}

			$propertyTypeHintDefinition = $this->parseType($phpcsFile, $propertyPointer, $varAnnotations[0]->getContent());

			if (($this->definitionContainsTraversableTypeHint($phpcsFile, $propertyPointer, $propertyTypeHintDefinition) && !$this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $propertyPointer, $propertyTypeHintDefinition))
				|| (
					$this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $propertyPointer, $propertyTypeHintDefinition)
					&& !$this->definitionContainsItemsSpecificationForTraversable($phpcsFile, $propertyPointer, $propertyTypeHintDefinition)
				)
			) {
				$phpcsFile->addError(
					sprintf(
						'@var annotation of property %s does not specify type hint for its items.',
						PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
					),
					$varAnnotations[0]->getStartPointer(),
					self::CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION
				);
			}
		}
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

	private function definitionContainsNullTypeHint(TypeNode $typeHintDefinition): bool
	{
		if ($typeHintDefinition instanceof IdentifierTypeNode && $typeHintDefinition->name === 'null') {
			return true;
		}

		if($typeHintDefinition instanceof UnionTypeNode) {
			foreach($typeHintDefinition->types as $type) {
				if($this->definitionContainsNullTypeHint($type)) {
					return true;
				}
			}
		}

		return false;
	}

	private function definitionContainsStaticOrThisTypeHint(TypeNode $typeHintDefinition): bool
	{
		switch (true) {
			case $typeHintDefinition instanceof ThisTypeNode:
				return true;
			case $typeHintDefinition instanceof IdentifierTypeNode:
				return $typeHintDefinition->name === 'static';
			default:
				return false;
		}
	}

	private function definitionContainsOneTypeHint(TypeNode $typeHintDefinition): bool
	{
		return !$typeHintDefinition instanceof UnionTypeNode;
	}

	private function definitionContainsJustTwoTypeHints(UnionTypeNode $typeHintDefinition): bool
	{
		return count($typeHintDefinition->types) === 2;
	}

	private function definitionContainsTraversableTypeHint(File $phpcsFile, int $pointer, TypeNode $typeHintDefinition): bool
	{
	    switch(true) {
            case $typeHintDefinition instanceof UnionTypeNode:
                return array_reduce($typeHintDefinition->types, function (bool $carry, TypeNode $typeHint) use ($phpcsFile, $pointer): bool {
                    return $this->definitionContainsTraversableTypeHint($phpcsFile, $pointer, $typeHint) ? true : $carry;
                }, false);
            case $typeHintDefinition instanceof IntersectionTypeNode:
                foreach ($typeHintDefinition->types as $type) {
                    if ($this->definitionContainsTraversableTypeHint($phpcsFile, $pointer, $type)) {
                        return true;
                    }
                }
                return false;
            case $typeHintDefinition instanceof ArrayTypeNode:
                return true;
            case $typeHintDefinition instanceof GenericTypeNode:
                return $this->definitionContainsTraversableTypeHint($phpcsFile, $pointer, $typeHintDefinition->type);
			case $typeHintDefinition instanceof IdentifierTypeNode:
				return $this->isTraversableTypeHint($typeHintDefinition->name);
            default:
                return false;
        }
	}

	private function isTraversableTypeHint(string $typeHint): bool
	{
		return TypeHintHelper::isSimpleIterableTypeHint($typeHint) || array_key_exists($typeHint, $this->getNormalizedTraversableTypeHints());
	}

	private function parseType(File $phpcsFile, int $pointer, string $type): TypeNode {
        $type = (new TypeParser())->parse(new TokenIterator((new Lexer())->tokenize($type)));

        $this->fixType($phpcsFile, $pointer, $type);

        return $type;
    }

    private function fixType(File $phpcsFile, int $pointer, TypeNode $typeNode): void {
		switch(true) {
			case $typeNode instanceof GenericTypeNode:
				$this->fixType($phpcsFile, $pointer, $typeNode->type);
				foreach ($typeNode->genericTypes as $genericType) {
					$this->fixType($phpcsFile, $pointer, $genericType);
				}
				break;
			case $typeNode instanceof ArrayTypeNode:
			case $typeNode instanceof NullableTypeNode:
				$this->fixType($phpcsFile, $pointer, $typeNode->type);
				break;
			case $typeNode instanceof IntersectionTypeNode:
			case $typeNode instanceof UnionTypeNode:
				foreach ($typeNode->types as $type) {
					$this->fixType($phpcsFile, $pointer, $type);
				}
				break;
			case $typeNode instanceof IdentifierTypeNode:
				$typeNode->name = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $pointer, $typeNode->name);
				break;
		}
	}

/*
	private function definitionContainsTraversableGeneric(string $typeHintDefinition): bool
    {
        if ('' === $typeHintDefinition) {
            return false;
        }

        $type = (new TypeParser())->parse(new TokenIterator((new Lexer())->tokenize($typeHintDefinition)));

        return $type instanceof GenericTypeNode;
    }*/

    private function definitionContainsTraversableTypeHintSpecification(File $phpcsFile, int $pointer, TypeNode $typeHintDefinition): bool
	{
		/*if ($typeHintDefinition instanceof ArrayTypeNode) {
			return true;
		}*/

		if($typeHintDefinition instanceof UnionTypeNode) {
			foreach($typeHintDefinition->types as $type) {
				if($this->definitionContainsTraversableTypeHintSpecification($phpcsFile, $pointer, $type)) {
					return true;
				}
			}
		}

		if ($typeHintDefinition instanceof GenericTypeNode) {
			$typeHintDefinition = $typeHintDefinition->type;
		}

		if($typeHintDefinition instanceof IdentifierTypeNode && $typeHintDefinition->name === '\array') { // TODO huh?
			return true;
		}

		return $this->definitionContainsTraversableTypeHint($phpcsFile, $pointer, $typeHintDefinition);
	}

	private function definitionContainsItemsSpecificationForTraversable(File $phpcsFile, int $pointer, TypeNode $typeHintDefinition): bool
	{
		if($typeHintDefinition instanceof ArrayTypeNode) {
			if($typeHintDefinition->type instanceof IdentifierTypeNode) {
				return !$this->isTraversableTypeHint($typeHintDefinition->type->name);
			}

			return true;
		}

		if($typeHintDefinition instanceof GenericTypeNode) {
			return $this->definitionContainsTraversableTypeHint($phpcsFile, $pointer, $typeHintDefinition->type);
		}

		if($typeHintDefinition instanceof UnionTypeNode) {
			foreach($typeHintDefinition->types as $type) {
				if($this->definitionContainsItemsSpecificationForTraversable($phpcsFile, $pointer, $type)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @return int[] [string => int]
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

	/**
	 * @return string[]
	 */
	private function getNormalizedUsefulAnnotations(): array
	{
		if ($this->normalizedUsefulAnnotations === null) {
			$this->normalizedUsefulAnnotations = SniffSettingsHelper::normalizeArray($this->usefulAnnotations);
		}
		return $this->normalizedUsefulAnnotations;
	}

	private function getFunctionTypeLabel(File $phpcsFile, int $functionPointer): string
	{
		return FunctionHelper::isMethod($phpcsFile, $functionPointer) ? 'Method' : 'Function';
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 * @return mixed[][] [string => [int, string]]
	 */
	private function getFunctionParameterTypeHintsDefinitions(File $phpcsFile, int $functionPointer): array
	{
		$parametersNames = FunctionHelper::getParametersNames($phpcsFile, $functionPointer);
		$parametersTypeHintsDefinitions = [];
		$lexer = new Lexer();
		$parser = new TypeParser();
		foreach (FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer) as $parameterAnnotationNo => $parameterAnnotation) {
			if ($parameterAnnotation->getContent() === null) {
				continue;
			}

            $tokens = $lexer->tokenize($parameterAnnotation->getContent());
            $parsed = $parser->parse(new TokenIterator($tokens));

			$parametersTypeHintsDefinitions[$parametersNames[$parameterAnnotationNo]] = ['pointer' => $parameterAnnotation->getStartPointer(), 'definition' => $parsed];
		}

		return $parametersTypeHintsDefinitions;
	}

	private function typeHintEqualsAnnotation(File $phpcsFile, int $functionPointer, string $typeHint, string $typeHintInAnnotation): bool
	{
		return TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHint) === TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHintInAnnotation);
	}

	private function isReturnAnnotationUseless(File $phpcsFile, int $functionPointer, ?ReturnTypeHint $returnTypeHint = null, ?Annotation $returnAnnotation = null): bool
	{
		if ($returnTypeHint === null || $returnAnnotation === null || $returnAnnotation->getContent() === null) {
			return false;
		}

		if (preg_match('~^\\S+\\s+\\S+~', $returnAnnotation->getContent())) {
			return false;
		}

		$returnTypeHintDefinition = $this->parseType($phpcsFile, $functionPointer, $returnAnnotation->getContent());

		if ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $this->parseType($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint()))) {
			return false;
		}

		if ($this->definitionContainsStaticOrThisTypeHint($returnTypeHintDefinition)) {
			return false;
		}

		if ($this->enableNullableTypeHints && $this->isTypeHintDefinitionCompoundOfNull($returnTypeHintDefinition)) {
			return $this->typeHintEqualsAnnotation($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint(), $this->getTypeFromNullableTypeHintDefinition($returnTypeHintDefinition));
		}

		if (!$this->definitionContainsOneTypeHint($returnTypeHintDefinition)) {
			return false;
		}

		if (!$this->typeHintEqualsAnnotation($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint(), (string) $returnTypeHintDefinition)) {
			return false;
		}

		return true;
	}

	private function isTypeHintDefinitionCompoundOfNull(string $definition): bool
	{
		return $this->definitionContainsJustTwoTypeHints($definition) && $this->definitionContainsNullTypeHint($definition);
	}

	private function getTypeFromNullableTypeHintDefinition(string $definition): string
	{
		$defitionParts = explode('|', $definition);
		return strtolower($defitionParts[0]) === 'null' ? $defitionParts[1] : $defitionParts[0];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 * @param \SlevomatCodingStandard\Helpers\ParameterTypeHint[]|null[] $functionTypeHints
	 * @param mixed[][] $parametersTypeHintsDefinitions
	 * @param bool[] $parametersContainDescription
	 * @return mixed[][]
	 */
	private function getUselessParameterAnnotations(File $phpcsFile, int $functionPointer, array $functionTypeHints, array $parametersTypeHintsDefinitions, array $parametersContainDescription): array
	{
		$uselessParameterNames = [];

		foreach ($functionTypeHints as $parameterName => $parameterTypeHint) {
			if ($parameterTypeHint === null) {
				continue;
			}

			if (!array_key_exists($parameterName, $parametersTypeHintsDefinitions)) {
				continue;
			}

			if (array_key_exists($parameterName, $parametersContainDescription)) {
				continue;
			}

			if ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $this->parseType($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint()))) {
				continue;
			}

			/** @var string $parameterTypeHintDefinition */
			$parameterTypeHintDefinition = $parametersTypeHintsDefinitions[$parameterName]['definition'];
			if ($this->definitionContainsStaticOrThisTypeHint($parameterTypeHintDefinition)) {
				continue;
//			} elseif ($this->isTypeHintDefinitionCompoundOfNull($parameterTypeHintDefinition)) {
//				if (!$this->typeHintEqualsAnnotation($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint(), $this->getTypeFromNullableTypeHintDefinition($parameterTypeHintDefinition))) {
//					continue;
//				}
			} elseif (!$this->definitionContainsOneTypeHint($parameterTypeHintDefinition)) {
				continue;
			} elseif (!$this->typeHintEqualsAnnotation($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint(), (string) $parameterTypeHintDefinition)) {
				continue;
			}

			$uselessParameterNames[] = ['pointer' => $parametersTypeHintsDefinitions[$parameterName]['pointer'], 'parameterName' => $parameterName];
		}

		return $uselessParameterNames;
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
