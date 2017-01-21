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

	/** @var int[] [string => int] */
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
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_PARAMETER_TYPE_HINT))) {
			return;
		}

		$parametersWithoutTypeHint = FunctionHelper::getParametersWithoutTypeHint($phpcsFile, $functionPointer);
		if (count($parametersWithoutTypeHint) === 0) {
			return;
		}

		$parametersNames = FunctionHelper::getParametersNames($phpcsFile, $functionPointer);

		$parametersTypeHintsDefinitions = [];
		foreach (FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer) as $parameterAnnotationNo => $parameterAnnotation) {
			$parameterAnnotationParts = preg_split('~\\s+~', $parameterAnnotation->getContent(), 2);
			$parameterTypeHintDefinition = $parameterAnnotationParts[0];
			if (isset($parameterAnnotationParts[1]) && preg_match('~^\.{3}\\s*(\$.+)~', $parameterAnnotationParts[1], $matches)) {
				$parametersTypeHintsDefinitions[$matches[1]] = $parameterTypeHintDefinition;
			} elseif (isset($parametersNames[$parameterAnnotationNo])) {
				$parametersTypeHintsDefinitions[$parametersNames[$parameterAnnotationNo]] = $parameterTypeHintDefinition;
			}
		}

		foreach ($parametersWithoutTypeHint as $parameterName) {
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
				if ($this->definitionContainsMixedTypeHint($parameterTypeHintDefinition) && preg_match('~\[\]$~', $parameterTypeHintDefinition)) {
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
					if (preg_match('~\[\]$~', $possibleParameterTypeHint)) {
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
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::CODE_MISSING_RETURN_TYPE_HINT))) {
			return;
		}

		if (FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer)) {
			return;
		}

		$isAbstract = FunctionHelper::isAbstract($phpcsFile, $functionPointer);
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
		$hasReturnAnnotation = $returnAnnotation !== null && $returnAnnotation->getContent() !== null;
		$returnTypeHintDefinition = $hasReturnAnnotation ? preg_split('~\\s+~', $returnAnnotation->getContent())[0] : null;

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
			if ($this->definitionContainsMixedTypeHint($returnTypeHintDefinition) && preg_match('~\[\]$~', $returnTypeHintDefinition)) {
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

					if (preg_match('~\[\]$~', $possibleReturnTypeHint)) {
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

		$isAbstract = FunctionHelper::isAbstract($phpcsFile, $functionPointer);

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
				if ($this->enableNullableTypeHints && $this->definitionContainsJustTwoTypeHints($returnAnnotation->getContent()) && $this->definitionContainsNullTypeHint($returnAnnotation->getContent())) {
					// Report error
				} elseif (!$this->definitionContainsOneTypeHint($returnAnnotation->getContent())) {
					return;
				} elseif ($returnTypeHint !== null && TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHint->getTypeHint()) === 'self' && $this->definitionContainsStaticOrThisTypeHint($returnAnnotation->getContent())) {
					return;
				}
			}
		}

		foreach (FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer) as $parameterTypeHint) {
			if ($parameterTypeHint === null || $this->isTraversableTypeHint(TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHint->getTypeHint()))) {
				return;
			}
		}

		foreach (AnnotationHelper::getAnnotations($phpcsFile, $functionPointer) as list($annotation)) {
			if ($annotation->getName() === SuppressHelper::ANNOTATION || array_key_exists($annotation->getName(), $this->getNormalizedUsefulAnnotations())) {
				return;
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
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $this->getSniffName(self::CODE_MISSING_PROPERTY_TYPE_HINT))) {
			return;
		}

		$varAnnotations = AnnotationHelper::getAnnotationsByName($phpcsFile, $propertyPointer, '@var');
		if (count($varAnnotations) === 0) {
			$phpcsFile->addError(
				sprintf(
					'Property %s does not have @var annotation.',
					PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
				),
				$propertyPointer,
				self::CODE_MISSING_PROPERTY_TYPE_HINT
			);
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

	private function definitionContainsMixedTypeHint(string $typeHintDefinition): bool
	{
		return preg_match('~(?:^mixed(?:\[\])?$)|(?:^mixed(?:\[\])?\|)|(?:\|mixed(?:\[\])?\|)|(?:\|mixed(?:\[\])?$)~i', $typeHintDefinition) !== 0;
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

	private function definitionContainsTraversableTypeHint(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer, string $typeHintDefinition): bool
	{
		if (!preg_match('~\[\](?:\||$)~', $typeHintDefinition)) {
			return false;
		}

		return array_reduce(explode('|', $typeHintDefinition), function (bool $carry, string $typeHint) use ($phpcsFile, $functionPointer): bool {
			$fullyQualifiedTypeHint = TypeHintHelper::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHint);
			if ($this->isTraversableTypeHint($fullyQualifiedTypeHint)) {
				$carry = true;
			}
			return $carry;
		}, false);
	}

	private function isTraversableTypeHint(string $typeHint): bool
	{
		return TypeHintHelper::isSimpleIterableTypeHint($typeHint) || array_key_exists($typeHint, $this->getNormalizedTraversableTypeHints());
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
	 * @return int[] [string => int]
	 */
	private function getNormalizedUsefulAnnotations(): array
	{
		if ($this->normalizedUsefulAnnotations === null) {
			$this->normalizedUsefulAnnotations = array_flip(SniffSettingsHelper::normalizeArray($this->usefulAnnotations));
		}
		return $this->normalizedUsefulAnnotations;
	}

	private function getFunctionTypeLabel(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer): string
	{
		return FunctionHelper::isMethod($phpcsFile, $functionPointer) ? 'Method' : 'Function';
	}

}
