<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;

class TypeHintDeclarationSniff implements \PHP_CodeSniffer_Sniff
{

	const NAME = 'SlevomatCodingStandard.TypeHints.TypeHintDeclaration';

	const CODE_MISSING_PARAMETER_TYPE_HINT = 'MissingParameterTypeHint';

	const CODE_MISSING_PROPERTY_TYPE_HINT = 'MissingPropertyTypeHint';

	const CODE_MISSING_RETURN_TYPE_HINT = 'MissingReturnTypeHint';

	const CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION = 'MissingTraversableParameterTypeHintSpecification';

	const CODE_MISSING_TRAVERSABLE_PROPERTY_TYPE_HINT_SPECIFICATION = 'MissingTraversablePropertyTypeHintSpecification';

	const CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION = 'MissingTraversableReturnTypeHintSpecification';

	const CODE_USELESS_DOC_COMMENT = 'UselessDocComment';

	/** @var bool */
	public $enableNullableTypeHints = PHP_VERSION_ID >= 70100;

	/** @var bool */
	public $enableVoidTypeHint = PHP_VERSION_ID >= 70100;

	/** @var string[] */
	public $traversableTypeHints = [];

	/** @var string[] */
	public $usefulAnnotations = [];

	/** @var int[] [string => int] */
	private $normalizedTraversableTypeHints;

	/** @var string[] */
	private $normalizedUsefulAnnotations;

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $pointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $pointer)
	{
		$token = $phpcsFile->getTokens()[$pointer];

		if ($token['code'] === T_FUNCTION) {
			$this->checkParametersTypeHints($phpcsFile, $pointer);
			$this->checkReturnTypeHints($phpcsFile, $pointer);
			$this->checkUselessDocComment($phpcsFile, $pointer);
		} elseif ($token['code'] === T_VARIABLE && PropertyHelper::isProperty($phpcsFile, $pointer)) {
			$this->checkPropertyTypeHint($phpcsFile, $pointer);
		}
	}

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_VARIABLE,
		];
	}

	private function checkParametersTypeHints(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer)
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, self::NAME)) {
			return;
		}

		$parametersTypeHintsDefinitions = $this->getFunctionParameterTypeHintsDefinitions($phpcsFile, $functionPointer);

		if (!SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_PARAMETER_TYPE_HINT_SPECIFICATION))) {
			foreach (FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer) as $parameterName => $parameterTypeHint) {
				$traversableTypeHint = false;
				if ($parameterTypeHint !== null && $this->isTraversableTypeHint($parameterTypeHint->getTypeHint())) {
					$traversableTypeHint = true;
				} elseif (array_key_exists($parameterName, $parametersTypeHintsDefinitions) && $this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $parametersTypeHintsDefinitions[$parameterName])) {
					$traversableTypeHint = true;
				}

				if (!$traversableTypeHint) {
					continue;
				}

				if (!array_key_exists($parameterName, $parametersTypeHintsDefinitions)) {
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
				} elseif (!$this->definitionContainsTraversableTypeHintSpeficication($parametersTypeHintsDefinitions[$parameterName]) || !$this->definitionContainsItemsSpecificationForTraversable($parametersTypeHintsDefinitions[$parameterName])) {
					$phpcsFile->addError(
						sprintf(
							'@param annotation of %s %s() does not specify type hint for items of its traversable parameter %s.',
							lcfirst($this->getFunctionTypeLabel($phpcsFile, $functionPointer)),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
							$parameterName
						),
						$functionPointer,
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

			$parameterTypeHintDefinition = $parametersTypeHintsDefinitions[$parameterName];

			if (strtolower($parameterTypeHintDefinition) === 'null') {
				continue;
			}

			if ($this->definitionContainsOneTypeHint($parameterTypeHintDefinition)) {
				if ($this->definitionContainsTraversableTypeHintSpeficication($parameterTypeHintDefinition)) {
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
				} elseif ($this->isValidTypeHint($parameterTypeHintDefinition)) {
					$possibleParameterTypeHint = $parameterTypeHintDefinition;
					$nullableParameterTypeHint = false;
				} else {
					return;
				}
			} elseif ($this->definitionContainsJustTwoTypeHints($parameterTypeHintDefinition)) {
				if ($this->definitionContainsNullTypeHint($parameterTypeHintDefinition)) {
					$parameterTypeHintDefinitionParts = explode('|', $parameterTypeHintDefinition);
					$possibleParameterTypeHint = strtolower($parameterTypeHintDefinitionParts[0]) === 'null' ? $parameterTypeHintDefinitionParts[1] : $parameterTypeHintDefinitionParts[0];
					$nullableParameterTypeHint = true;
					if ($this->definitionContainsTraversableTypeHintSpeficication($possibleParameterTypeHint)) {
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
					} elseif (!$this->isValidTypeHint($possibleParameterTypeHint)) {
						return;
					}
				} elseif ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $parameterTypeHintDefinition)) {
					$parameterTypeHintDefinitionParts = explode('|', $parameterTypeHintDefinition);
					$possibleParameterTypeHint = $this->isTraversableTypeHint(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHintDefinitionParts[0])) ? $parameterTypeHintDefinitionParts[0] : $parameterTypeHintDefinitionParts[1];
					$nullableParameterTypeHint = false;
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
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();

				$parameterTypeHint = TypeHintHelper::isSimpleTypeHint($possibleParameterTypeHint) ? TypeHintHelper::convertLongSimpleTypeHintToShort($possibleParameterTypeHint) : $possibleParameterTypeHint;

				$tokens = $phpcsFile->getTokens();
				$parameterPointer = $phpcsFile->findNext(T_VARIABLE, $tokens[$functionPointer]['parenthesis_opener'], $tokens[$functionPointer]['parenthesis_closer'], false, $parameterName);

				$beforeParameterPointer = $parameterPointer;
				do {
					$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $beforeParameterPointer - 1, $tokens[$functionPointer]['parenthesis_opener'] + 1);
					if ($previousPointer !== null && in_array($tokens[$previousPointer]['code'], [T_BITWISE_AND, T_ELLIPSIS], true)) {
						$beforeParameterPointer = $previousPointer;
					} else {
						break;
					}
				} while (true);

				if ($this->enableNullableTypeHints) {
					$phpcsFile->fixer->addContentBefore($beforeParameterPointer, sprintf('%s%s ', ($nullableParameterTypeHint ? '?' : ''), $parameterTypeHint));
				} else {
					$phpcsFile->fixer->addContentBefore($beforeParameterPointer, sprintf('%s ', $parameterTypeHint));
					if ($nullableParameterTypeHint) {
						$phpcsFile->fixer->addContent($parameterPointer, ' = null');
					}
				}

				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	private function checkReturnTypeHints(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer)
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, self::NAME)) {
			return;
		}

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
		$hasReturnAnnotation = $returnAnnotation !== null && $returnAnnotation->getContent() !== null;
		$returnTypeHintDefinition = $hasReturnAnnotation ? preg_split('~\\s+~', $returnAnnotation->getContent())[0] : '';

		$traversableTypeHint = false;
		if ($returnTypeHint !== null && $this->isTraversableTypeHint($returnTypeHint->getTypeHint())) {
			$traversableTypeHint = true;
		} elseif ($hasReturnAnnotation && $this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $returnTypeHintDefinition)) {
			$traversableTypeHint = true;
		}

		if ($traversableTypeHint && !SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION))) {
			if (!$hasReturnAnnotation) {
				$phpcsFile->addError(
					sprintf(
						'%s %s() does not have @return annotation for its traversable return value.',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
					),
					$functionPointer,
					self::CODE_MISSING_TRAVERSABLE_RETURN_TYPE_HINT_SPECIFICATION
				);
			} elseif (!$this->definitionContainsTraversableTypeHintSpeficication($returnTypeHintDefinition) || !$this->definitionContainsItemsSpecificationForTraversable($returnTypeHintDefinition)) {
				$phpcsFile->addError(
					sprintf(
						'@return annotation of %s %s() does not specify type hint for items of its traversable return value.',
						lcfirst($this->getFunctionTypeLabel($phpcsFile, $functionPointer)),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
					),
					$functionPointer,
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
			if ($this->definitionContainsTraversableTypeHintSpeficication($returnTypeHintDefinition)) {
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
			} elseif ($this->isValidTypeHint($returnTypeHintDefinition)) {
				$possibleReturnTypeHint = $returnTypeHintDefinition;
				$nullableReturnTypeHint = false;
			} else {
				return;
			}
		} elseif ($this->definitionContainsJustTwoTypeHints($returnTypeHintDefinition)) {
			if ($this->definitionContainsNullTypeHint($returnTypeHintDefinition)) {
				if ($this->enableNullableTypeHints) {
					$returnTypeHintDefinitionParts = explode('|', $returnTypeHintDefinition);
					$possibleReturnTypeHint = strtolower($returnTypeHintDefinitionParts[0]) === 'null' ? $returnTypeHintDefinitionParts[1] : $returnTypeHintDefinitionParts[0];
					$nullableReturnTypeHint = true;

					if ($this->definitionContainsTraversableTypeHintSpeficication($possibleReturnTypeHint)) {
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
					} elseif (!$this->isValidTypeHint($possibleReturnTypeHint)) {
						return;
					}
				} else {
					return;
				}
			} elseif ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $returnTypeHintDefinition)) {
				$returnTypeHintDefinitionParts = explode('|', $returnTypeHintDefinition);
				$possibleReturnTypeHint = $this->isTraversableTypeHint(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHintDefinitionParts[0])) ? $returnTypeHintDefinitionParts[0] : $returnTypeHintDefinitionParts[1];
				$nullableReturnTypeHint = false;
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
		if ($fix) {
			$phpcsFile->fixer->beginChangeset();
			$returnTypeHint = TypeHintHelper::isSimpleTypeHint($possibleReturnTypeHint) ? TypeHintHelper::convertLongSimpleTypeHintToShort($possibleReturnTypeHint) : $possibleReturnTypeHint;
			$phpcsFile->fixer->addContent($phpcsFile->getTokens()[$functionPointer]['parenthesis_closer'], sprintf(': %s%s', ($nullableReturnTypeHint ? '?' : ''), $returnTypeHint));
			$phpcsFile->fixer->endChangeset();
		}
	}

	private function checkUselessDocComment(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer)
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_USELESS_DOC_COMMENT))) {
			return;
		}

		if (!DocCommentHelper::hasDocComment($phpcsFile, $functionPointer)) {
			return;
		}

		if (DocCommentHelper::hasDocCommentDescription($phpcsFile, $functionPointer)) {
			return;
		}

		foreach (FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer) as $parameterAnnotation) {
			if (preg_match('~^\\S+\\s+(?:(?:\.{3}\\s*)?\$\\S+\\s+)?[^$]~', $parameterAnnotation->getContent())) {
				return;
			}
		}

		$isAbstract = FunctionHelper::isAbstract($phpcsFile, $functionPointer);

		$typeHintEqualsAnnotation = function (string $typeHint, string $typeHintInAnnotation) use ($phpcsFile, $functionPointer): bool {
			return TypeHintHelper::isSimpleTypeHint($typeHint) || TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHint) === TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHintInAnnotation);
		};

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		if ($isAbstract || FunctionHelper::returnsValue($phpcsFile, $functionPointer)) {
			if ($returnTypeHint === null) {
				return;
			}

			if ($this->isTraversableTypeHint(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint()))) {
				return;
			}

			$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
			if ($returnAnnotation !== null) {
				if (preg_match('~^\\S+\\s+\\S+~', $returnAnnotation->getContent())) {
					return;
				}

				$returnTypeHintsDefinition = preg_split('~\\s+~', $returnAnnotation->getContent())[0];
				if ($this->definitionContainsStaticOrThisTypeHint($returnTypeHintsDefinition)) {
					return;
				} elseif ($this->enableNullableTypeHints && $this->definitionContainsJustTwoTypeHints($returnTypeHintsDefinition) && $this->definitionContainsNullTypeHint($returnTypeHintsDefinition)) {
					$returnTypeHintDefinitionParts = explode('|', $returnTypeHintsDefinition);
					$returnTypeHintInAnnotation = strtolower($returnTypeHintDefinitionParts[0]) === 'null' ? $returnTypeHintDefinitionParts[1] : $returnTypeHintDefinitionParts[0];
					if (!$typeHintEqualsAnnotation($returnTypeHint->getTypeHint(), $returnTypeHintInAnnotation)) {
						return;
					}
				} elseif (!$this->definitionContainsOneTypeHint($returnTypeHintsDefinition)) {
					return;
				} elseif (!$typeHintEqualsAnnotation($returnTypeHint->getTypeHint(), $returnTypeHintsDefinition)) {
					return;
				}
			}
		}

		$parametersTypeHintsDefinitions = $this->getFunctionParameterTypeHintsDefinitions($phpcsFile, $functionPointer);
		foreach (FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer) as $parameterName => $parameterTypeHint) {
			if ($parameterTypeHint === null) {
				return;
			}

			if ($this->isTraversableTypeHint(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint()))) {
				return;
			}

			if (array_key_exists($parameterName, $parametersTypeHintsDefinitions)) {
				$parameterTypeHintDefinition = $parametersTypeHintsDefinitions[$parameterName];
				if ($this->definitionContainsStaticOrThisTypeHint($parameterTypeHintDefinition)) {
					return;
				} elseif ($this->definitionContainsJustTwoTypeHints($parameterTypeHintDefinition) && $this->definitionContainsNullTypeHint($parameterTypeHintDefinition)) {
					$parameterTypeHintDefinitionParts = explode('|', $parameterTypeHintDefinition);
					$parameterTypeHintInAnnotation = strtolower($parameterTypeHintDefinitionParts[0]) === 'null' ? $parameterTypeHintDefinitionParts[1] : $parameterTypeHintDefinitionParts[0];
					if (!$typeHintEqualsAnnotation($parameterTypeHint->getTypeHint(), $parameterTypeHintInAnnotation)) {
						return;
					}
				} elseif (!$this->definitionContainsOneTypeHint($parameterTypeHintDefinition)) {
					return;
				} elseif (!$typeHintEqualsAnnotation($parameterTypeHint->getTypeHint(), $parameterTypeHintDefinition)) {
					return;
				}
			}
		}

		foreach (AnnotationHelper::getAnnotations($phpcsFile, $functionPointer) as list($annotation)) {
			if ($annotation->getName() === SuppressHelper::ANNOTATION) {
				return;
			}

			foreach ($this->getNormalizedUsefulAnnotations() as $usefulAnnotation) {
				if ($annotation->getName() === $usefulAnnotation) {
					return;
				}

				if (substr($usefulAnnotation, -1) === '\\' && strpos($annotation->getName(), $usefulAnnotation) === 0) {
					return;
				}
			}
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
		if ($fix) {
			$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $functionPointer);
			$docCommentClosePointer = $phpcsFile->getTokens()[$docCommentOpenPointer]['comment_closer'];

			$changeStart = $docCommentOpenPointer;
			$changeEnd = TokenHelper::findNextEffective($phpcsFile, $docCommentClosePointer + 1) - 1;

			$phpcsFile->fixer->beginChangeset();
			for ($i = $changeStart; $i <= $changeEnd; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}
			$phpcsFile->fixer->endChangeset();
		}
	}

	private function checkPropertyTypeHint(\PHP_CodeSniffer_File $phpcsFile, int $propertyPointer)
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, self::NAME)) {
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

			$propertyTypeHintDefinition = preg_split('~\\s+~', (string) $varAnnotations[0]->getContent())[0];

			if ($this->definitionContainsTraversableTypeHint($phpcsFile, $propertyPointer, $propertyTypeHintDefinition) && !$this->definitionContainsTraversableTypeHintSpeficication($propertyTypeHintDefinition)) {
				$phpcsFile->addError(
					sprintf(
						'@var annotation of property %s does not specify type hint for its items.',
						PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
					),
					$propertyPointer,
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
		return TypeHintHelper::isSimpleTypeHint($typeHint) || !in_array($typeHint, TypeHintHelper::$simpleUnofficialTypeHints, true);
	}

	private function definitionContainsNullTypeHint(string $typeHintDefinition): bool
	{
		return preg_match('~(?:^null$)|(?:^null\|)|(?:\|null\|)|(?:\|null$)~i', $typeHintDefinition) !== 0;
	}

	private function definitionContainsStaticOrThisTypeHint(string $typeHintDefinition): bool
	{
		return preg_match('~(?:^static$)|(?:^static\|)|(?:\|static\|)|(?:\|static$)~i', $typeHintDefinition) !== 0
			|| preg_match('~(?:^\$this$)|(?:^\$this\|)|(?:\|\$this\|)|(?:\|\$this$)~i', $typeHintDefinition) !== 0;
	}

	private function definitionContainsOneTypeHint(string $typeHintDefinition): bool
	{
		return strpos($typeHintDefinition, '|') === false;
	}

	private function definitionContainsJustTwoTypeHints(string $typeHintDefinition): bool
	{
		return count(explode('|', $typeHintDefinition)) === 2;
	}

	private function definitionContainsTraversableTypeHint(\PHP_CodeSniffer_File $phpcsFile, int $pointer, string $typeHintDefinition): bool
	{
		return array_reduce(explode('|', $typeHintDefinition), function (bool $carry, string $typeHint) use ($phpcsFile, $pointer): bool {
			$fullyQualifiedTypeHint = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $pointer, $typeHint);
			return $this->isTraversableTypeHint($fullyQualifiedTypeHint) ? true : $carry;
		}, false);
	}

	private function isTraversableTypeHint(string $typeHint): bool
	{
		return TypeHintHelper::isSimpleIterableTypeHint($typeHint) || array_key_exists($typeHint, $this->getNormalizedTraversableTypeHints());
	}

	private function definitionContainsTraversableTypeHintSpeficication(string $typeHintDefinition): bool
	{
		return (bool) preg_match('~\[\](?:\||$)~', $typeHintDefinition);
	}

	private function definitionContainsItemsSpecificationForTraversable(string $typeHintDefinition): bool
	{
		if (preg_match_all('~(?<=^|\|)(.+?)\[\](?=\||$)~', $typeHintDefinition, $matches)) {
			foreach ($matches[1] as $returnTypeHintPart) {
				if (!$this->isTraversableTypeHint($returnTypeHintPart)) {
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

	private function getFunctionTypeLabel(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer): string
	{
		return FunctionHelper::isMethod($phpcsFile, $functionPointer) ? 'Method' : 'Function';
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $functionPointer
	 * @return string[] [string => string]
	 */
	private function getFunctionParameterTypeHintsDefinitions(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer): array
	{
		$parametersNames = FunctionHelper::getParametersNames($phpcsFile, $functionPointer);
		$parametersTypeHintsDefinitions = [];
		foreach (FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer) as $parameterAnnotationNo => $parameterAnnotation) {
			$parameterAnnotationParts = preg_split('~\\s+~', $parameterAnnotation->getContent(), 2);
			$parameterTypeHintDefinition = $parameterAnnotationParts[0];
			if (isset($parameterAnnotationParts[1]) && preg_match('~^(?:\.{3}\\s*)?(\$\\S+)~', $parameterAnnotationParts[1], $matches)) {
				$parametersTypeHintsDefinitions[$matches[1]] = $parameterTypeHintDefinition;
			} elseif (isset($parametersNames[$parameterAnnotationNo])) {
				$parametersTypeHintsDefinitions[$parametersNames[$parameterAnnotationNo]] = $parameterTypeHintDefinition;
			}
		}

		return $parametersTypeHintsDefinitions;
	}

}
