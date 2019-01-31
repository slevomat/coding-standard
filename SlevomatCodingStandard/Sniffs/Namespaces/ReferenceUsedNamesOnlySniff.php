<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\ConstantHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedName;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use stdClass;
use function array_filter;
use function array_flip;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_values;
use function constant;
use function count;
use function defined;
use function function_exists;
use function in_array;
use function sprintf;
use function strtolower;
use function substr;
use const T_COMMA;
use const T_DECLARE;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_NAMESPACE;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_TAG;
use const T_SEMICOLON;
use const T_WHITESPACE;

class ReferenceUsedNamesOnlySniff implements Sniff
{

	public const CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME = 'ReferenceViaFullyQualifiedName';

	public const CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE = 'ReferenceViaFullyQualifiedNameWithoutNamespace';

	public const CODE_REFERENCE_VIA_FALLBACK_GLOBAL_NAME = 'ReferenceViaFallbackGlobalName';

	public const CODE_PARTIAL_USE = 'PartialUse';

	/** @var bool */
	public $searchAnnotations = false;

	/** @var string[] */
	public $fullyQualifiedKeywords = [];

	/** @var string[]|null */
	private $normalizedFullyQualifiedKeywords;

	/** @var bool */
	public $allowFullyQualifiedExceptions = false;

	/** @var bool */
	public $allowFullyQualifiedGlobalClasses = false;

	/** @var bool */
	public $allowFullyQualifiedGlobalFunctions = false;

	/** @var bool */
	public $allowFallbackGlobalFunctions = true;

	/** @var bool */
	public $allowFullyQualifiedGlobalConstants = false;

	/** @var bool */
	public $allowFallbackGlobalConstants = true;

	/** @var string[] */
	public $specialExceptionNames = [];

	/** @var string[]|null */
	private $normalizedSpecialExceptionNames;

	/** @var string[] */
	public $ignoredNames = [];

	/** @var string[]|null */
	private $normalizedIgnoredNames;

	/** @var bool */
	public $allowPartialUses = true;

	/**
	 * If empty, all namespaces are required to be used
	 *
	 * @var string[]
	 */
	public $namespacesRequiredToUse = [];

	/** @var string[]|null */
	private $normalizedNamespacesRequiredToUse;

	/** @var bool */
	public $allowFullyQualifiedNameForCollidingClasses = false;

	/** @var bool */
	public $allowFullyQualifiedNameForCollidingFunctions = false;

	/** @var bool */
	public $allowFullyQualifiedNameForCollidingConstants = false;

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @return string[]
	 */
	private function getSpecialExceptionNames(): array
	{
		if ($this->normalizedSpecialExceptionNames === null) {
			$this->normalizedSpecialExceptionNames = SniffSettingsHelper::normalizeArray($this->specialExceptionNames);
		}

		return $this->normalizedSpecialExceptionNames;
	}

	/**
	 * @return string[]
	 */
	private function getIgnoredNames(): array
	{
		if ($this->normalizedIgnoredNames === null) {
			$this->normalizedIgnoredNames = SniffSettingsHelper::normalizeArray($this->ignoredNames);
		}

		return $this->normalizedIgnoredNames;
	}

	/**
	 * @return string[]
	 */
	private function getNamespacesRequiredToUse(): array
	{
		if ($this->normalizedNamespacesRequiredToUse === null) {
			$this->normalizedNamespacesRequiredToUse = SniffSettingsHelper::normalizeArray($this->namespacesRequiredToUse);
		}

		return $this->normalizedNamespacesRequiredToUse;
	}

	/**
	 * @return string[]
	 */
	private function getFullyQualifiedKeywords(): array
	{
		if ($this->normalizedFullyQualifiedKeywords === null) {
			$this->normalizedFullyQualifiedKeywords = array_map(function (string $keyword) {
				if (!defined($keyword)) {
					throw new UndefinedKeywordTokenException($keyword);
				}
				return constant($keyword);
			}, SniffSettingsHelper::normalizeArray($this->fullyQualifiedKeywords));
		}

		return $this->normalizedFullyQualifiedKeywords;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$references = $this->getReferences($phpcsFile, $openTagPointer);

		$definedClassesIndex = array_flip(array_map(function (string $className): string {
			return strtolower($className);
		}, ClassHelper::getAllNames($phpcsFile)));
		$definedFunctionsIndex = array_flip(array_map(function (string $functionName): string {
			return strtolower($functionName);
		}, FunctionHelper::getAllFunctionNames($phpcsFile)));
		$definedConstantsIndex = array_flip(ConstantHelper::getAllNames($phpcsFile));

		if ($this->allowFullyQualifiedNameForCollidingClasses) {
			$classReferences = array_filter($references, function (stdClass $reference): bool {
				return !$reference->fromDocComment && $reference->isClass;
			});

			$classReferencesIndex = [];
			foreach ($classReferences as $classReference) {
				$classReferencesIndex[strtolower($classReference->name)] = NamespaceHelper::resolveName($phpcsFile, $classReference->name, $classReference->type, $classReference->startPointer);
			}
		}

		if ($this->allowFullyQualifiedNameForCollidingFunctions) {
			$functionReferences = array_filter($references, function (stdClass $reference): bool {
				return !$reference->fromDocComment && $reference->isFunction;
			});

			$functionReferencesIndex = [];
			foreach ($functionReferences as $functionReference) {
				$functionReferencesIndex[strtolower($functionReference->name)] = NamespaceHelper::resolveName($phpcsFile, $functionReference->name, $functionReference->type, $functionReference->startPointer);
			}
		}

		if ($this->allowFullyQualifiedNameForCollidingConstants) {
			$constantReferences = array_filter($references, function (stdClass $reference): bool {
				return !$reference->fromDocComment && $reference->isConstant;
			});

			$constantReferencesIndex = [];
			foreach ($constantReferences as $constantReference) {
				$constantReferencesIndex[$constantReference->name] = NamespaceHelper::resolveName($phpcsFile, $constantReference->name, $constantReference->type, $constantReference->startPointer);
			}
		}

		foreach ($references as $reference) {
			$useStatements = UseStatementHelper::getUseStatementsForPointer($phpcsFile, $reference->startPointer);

			$name = $reference->name;
			$startPointer = $reference->startPointer;
			$canonicalName = NamespaceHelper::normalizeToCanonicalName($name);
			$unqualifiedName = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($name);

			$isFullyQualified = NamespaceHelper::isFullyQualifiedName($name);
			$isGlobalFallback = !$isFullyQualified
				&& !NamespaceHelper::hasNamespace($name)
				&& NamespaceHelper::findCurrentNamespaceName($phpcsFile, $startPointer) !== null
				&& !array_key_exists(UseStatement::getUniqueId($reference->type, $name), $useStatements);

			$isGlobalFunctionFallback = false;
			if ($reference->isFunction && $isGlobalFallback) {
				$isGlobalFunctionFallback = !array_key_exists(strtolower($reference->name), $definedFunctionsIndex) && function_exists($reference->name);
			}
			$isGlobalConstantFallback = false;
			if ($reference->isConstant && $isGlobalFallback) {
				$isGlobalConstantFallback = !array_key_exists($reference->name, $definedConstantsIndex) && defined($reference->name);
			}

			if ($isFullyQualified) {
				if ($reference->isClass && $this->allowFullyQualifiedNameForCollidingClasses) {
					$lowerCasedUnqualifiedClassName = strtolower($unqualifiedName);
					if (array_key_exists($lowerCasedUnqualifiedClassName, $definedClassesIndex)) {
						continue;
					}

					if (isset($classReferencesIndex[$lowerCasedUnqualifiedClassName]) && $name !== $classReferencesIndex[$lowerCasedUnqualifiedClassName]) {
						continue;
					}
				} elseif ($reference->isFunction && $this->allowFullyQualifiedNameForCollidingFunctions) {
					$lowerCasedUnqualifiedFunctionName = strtolower($unqualifiedName);
					if (array_key_exists($lowerCasedUnqualifiedFunctionName, $definedFunctionsIndex)) {
						continue;
					}
				} elseif ($reference->isConstant && $this->allowFullyQualifiedNameForCollidingConstants) {
					if (array_key_exists($unqualifiedName, $definedConstantsIndex)) {
						continue;
					}
				}
			}

			if ($isFullyQualified || $isGlobalFunctionFallback || $isGlobalConstantFallback) {
				if ($isFullyQualified && !$this->isRequiredToBeUsed($name)) {
					continue;
				}

				$isExceptionByName = StringHelper::endsWith($name, 'Exception')
					|| $name === '\Throwable'
					|| (StringHelper::endsWith($name, 'Error') && !NamespaceHelper::hasNamespace($name))
					|| in_array($canonicalName, $this->getSpecialExceptionNames(), true);
				$inIgnoredNames = in_array($canonicalName, $this->getIgnoredNames(), true);

				if ($isExceptionByName && !$inIgnoredNames && $this->allowFullyQualifiedExceptions) {
					continue;
				}

				$previousKeywordPointer = TokenHelper::findPreviousExcluding($phpcsFile, array_merge(TokenHelper::$nameTokenCodes, [T_WHITESPACE, T_COMMA]), $startPointer - 1);
				if (!in_array($tokens[$previousKeywordPointer]['code'], $this->getFullyQualifiedKeywords(), true)) {
					if (
						$isFullyQualified
						&& !NamespaceHelper::hasNamespace($name)
						&& NamespaceHelper::findCurrentNamespaceName($phpcsFile, $startPointer) === null
					) {
						$label = sprintf($reference->isConstant ? 'Constant %s' : ($reference->isFunction ? 'Function %s()' : 'Class %s'), $name);

						$fix = $phpcsFile->addFixableError(sprintf(
							'%s should not be referenced via a fully qualified name, but via an unqualified name without the leading \\, because the file does not have a namespace and the type cannot be put in a use statement.',
							$label
						), $startPointer, self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE);
						if ($fix) {
							$phpcsFile->fixer->beginChangeset();

							if ($reference->fromDocComment) {
								$fixedAnnotationContent = AnnotationHelper::fixAnnotation(
									$phpcsFile,
									$reference->annotation,
									$reference->nameNode,
									new IdentifierTypeNode(substr($reference->name, 1))
								);

								$phpcsFile->fixer->replaceToken($startPointer, $fixedAnnotationContent);
								for ($i = $startPointer + 1; $i <= $reference->endPointer; $i++) {
									$phpcsFile->fixer->replaceToken($i, '');
								}
							} else {
								$phpcsFile->fixer->replaceToken($startPointer, substr($tokens[$startPointer]['content'], 1));
							}

							$phpcsFile->fixer->endChangeset();
						}
					} else {
						$shouldBeUsed = NamespaceHelper::hasNamespace($name);
						if (!$shouldBeUsed) {
							if ($reference->isFunction) {
								$shouldBeUsed = $isFullyQualified ? !$this->allowFullyQualifiedGlobalFunctions : !$this->allowFallbackGlobalFunctions;
							} elseif ($reference->isConstant) {
								$shouldBeUsed = $isFullyQualified ? !$this->allowFullyQualifiedGlobalConstants : !$this->allowFallbackGlobalConstants;
							} else {
								$shouldBeUsed = !$this->allowFullyQualifiedGlobalClasses;
							}
						}

						if (!$shouldBeUsed) {
							continue;
						}

						$nameToReference = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($name);
						$canonicalNameToReference = $reference->isConstant ? $nameToReference : strtolower($nameToReference);

						$canBeFixed = true;
						foreach ($useStatements as $useStatement) {
							if ($useStatement->getType() !== $reference->type) {
								continue;
							}

							if ($useStatement->getFullyQualifiedTypeName() === $canonicalName) {
								continue;
							}

							if (!(
								$useStatement->getCanonicalNameAsReferencedInFile() === $canonicalNameToReference
								|| ($reference->isClass && array_key_exists($canonicalNameToReference, $definedClassesIndex))
								|| ($reference->isFunction && array_key_exists($canonicalNameToReference, $definedFunctionsIndex))
								|| ($reference->isConstant && array_key_exists($canonicalNameToReference, $definedConstantsIndex))
							)) {
								continue;
							}

							$canBeFixed = false;
							break;
						}

						$label = sprintf($reference->isConstant ? 'Constant %s' : ($reference->isFunction ? 'Function %s()' : 'Class %s'), $name);
						$errorCode = $isGlobalConstantFallback || $isGlobalFunctionFallback
							? self::CODE_REFERENCE_VIA_FALLBACK_GLOBAL_NAME
							: self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME;
						$errorMessage = $isGlobalConstantFallback || $isGlobalFunctionFallback
							? sprintf('%s should not be referenced via a fallback global name, but via a use statement.', $label)
							: sprintf('%s should not be referenced via a fully qualified name, but via a use statement.', $label);
						if ($canBeFixed) {
							$fix = $phpcsFile->addFixableError($errorMessage, $startPointer, $errorCode);
						} else {
							$phpcsFile->addError($errorMessage, $startPointer, $errorCode);
							$fix = false;
						}

						if ($fix) {
							$alreadyUsed = false;
							foreach ($useStatements as $useStatement) {
								if ($useStatement->getType() !== $reference->type || $useStatement->getFullyQualifiedTypeName() !== $canonicalName) {
									continue;
								}

								$nameToReference = $useStatement->getNameAsReferencedInFile();
								$alreadyUsed = true;
								break;
							}

							$phpcsFile->fixer->beginChangeset();

							if ($reference->fromDocComment) {
								$fixedAnnotationContent = AnnotationHelper::fixAnnotation(
									$phpcsFile,
									$reference->annotation,
									$reference->nameNode,
									new IdentifierTypeNode($nameToReference)
								);
								$phpcsFile->fixer->replaceToken($startPointer, $fixedAnnotationContent);
							} else {
								$phpcsFile->fixer->replaceToken($startPointer, $nameToReference);
							}

							for ($i = $startPointer + 1; $i <= $reference->endPointer; $i++) {
								$phpcsFile->fixer->replaceToken($i, '');
							}

							if (!$alreadyUsed) {
								$useStatementPlacePointer = $this->getUseStatementPlacePointer($phpcsFile, $openTagPointer, $useStatements);

								$useTypeName = UseStatement::getTypeName($reference->type);
								$useTypeFormatted = $useTypeName !== null ? sprintf('%s ', $useTypeName) : '';

								$phpcsFile->fixer->addNewline($useStatementPlacePointer);
								$phpcsFile->fixer->addContent($useStatementPlacePointer, sprintf('use %s%s;', $useTypeFormatted, $canonicalName));
							}

							$phpcsFile->fixer->endChangeset();
						}
					}
				}
			} elseif (!$this->allowPartialUses) {
				if (NamespaceHelper::isQualifiedName($name)) {
					$phpcsFile->addError(sprintf(
						'Partial use statements are not allowed, but referencing %s found.',
						$name
					), $startPointer, self::CODE_PARTIAL_USE);
				}
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 * @param \SlevomatCodingStandard\Helpers\UseStatement[] $useStatements
	 * @return int
	 */
	private function getUseStatementPlacePointer(File $phpcsFile, int $openTagPointer, array $useStatements): int
	{
		if (count($useStatements) !== 0) {
			$lastUseStatement = array_values($useStatements)[count($useStatements) - 1];
			/** @var int $useStatementPlacePointer */
			$useStatementPlacePointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $lastUseStatement->getPointer() + 1);
			return $useStatementPlacePointer;
		}

		$namespacePointer = TokenHelper::findNext($phpcsFile, T_NAMESPACE, $openTagPointer + 1);
		if ($namespacePointer !== null) {
			/** @var int $useStatementPlacePointer */
			$useStatementPlacePointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $namespacePointer + 1);
			return $useStatementPlacePointer;
		}

		$pointerAfterOpenTagPointer = TokenHelper::findNextEffective($phpcsFile, $openTagPointer + 1);
		if ($phpcsFile->getTokens()[$pointerAfterOpenTagPointer]['code'] === T_DECLARE) {
			/** @var int $useStatementPlacePointer */
			$useStatementPlacePointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $pointerAfterOpenTagPointer + 1);
			return $useStatementPlacePointer;
		}

		return $openTagPointer;
	}

	private function isRequiredToBeUsed(string $name): bool
	{
		if (count($this->namespacesRequiredToUse) === 0) {
			return true;
		}

		foreach ($this->getNamespacesRequiredToUse() as $namespace) {
			if (!NamespaceHelper::isTypeInNamespace($name, $namespace)) {
				continue;
			}

			return true;
		}

		return false;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 * @return \stdClass[]
	 */
	private function getReferences(File $phpcsFile, int $openTagPointer): array
	{
		$tokens = $phpcsFile->getTokens();

		$references = [];
		foreach (ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer) as $referencedName) {
			$reference = new stdClass();
			$reference->fromDocComment = false;
			$reference->name = $referencedName->getNameAsReferencedInFile();
			$reference->type = $referencedName->getType();
			$reference->startPointer = $referencedName->getStartPointer();
			$reference->endPointer = $referencedName->getEndPointer();
			$reference->isClass = $referencedName->isClass();
			$reference->isConstant = $referencedName->isConstant();
			$reference->isFunction = $referencedName->isFunction();

			$references[] = $reference;
		}

		if (!$this->searchAnnotations) {
			return $references;
		}

		$searchAnnotationsPointer = $openTagPointer + 1;
		while (true) {
			$docCommentOpenPointer = TokenHelper::findNext($phpcsFile, T_DOC_COMMENT_OPEN_TAG, $searchAnnotationsPointer);
			if ($docCommentOpenPointer === null) {
				break;
			}

			$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

			foreach ($annotations as $annotationsByName) {
				foreach ($annotationsByName as $annotation) {
					if ($annotation instanceof GenericAnnotation) {
						continue;
					}

					if ($annotation->isInvalid()) {
						continue;
					}

					foreach (AnnotationHelper::getAnnotationTypes($annotation) as $annotationType) {
						foreach (AnnotationTypeHelper::getIdentifierTypeNodes($annotationType) as $typeHintNode) {
							$typeHint = AnnotationTypeHelper::getTypeHintFromNode($typeHintNode);

							$lowercasedTypeHint = strtolower($typeHint);
							if (
								TypeHintHelper::isSimpleTypeHint($lowercasedTypeHint)
								|| TypeHintHelper::isSimpleUnofficialTypeHints($lowercasedTypeHint)
								|| !TypeHelper::isTypeName($typeHint)
							) {
								continue;
							}

							$reference = new stdClass();
							$reference->fromDocComment = true;
							$reference->annotation = $annotation;
							$reference->nameNode = $typeHintNode;
							$reference->name = $typeHint;
							$reference->type = ReferencedName::TYPE_DEFAULT;
							$reference->startPointer = $annotation->getStartPointer();
							$reference->endPointer = $annotation->getEndPointer();
							$reference->isClass = true;
							$reference->isConstant = false;
							$reference->isFunction = false;

							$references[] = $reference;
						}
					}
				}
			}

			$searchAnnotationsPointer = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
		}

		return $references;
	}

}
