<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\CommentHelper;
use SlevomatCodingStandard\Helpers\ConstantHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
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
use function array_reduce;
use function array_values;
use function count;
use function defined;
use function function_exists;
use function in_array;
use function preg_quote;
use function preg_replace;
use function sprintf;
use function strpos;
use function strtolower;
use function substr;
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

	private const SOURCE_CODE = 1;
	private const SOURCE_ANNOTATION = 2;
	private const SOURCE_ANNOTATION_CONSTANT_FETCH = 3;
	private const SOURCE_ATTRIBUTE = 4;

	/** @var bool */
	public $searchAnnotations = false;

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

	/** @var list<string> */
	public $specialExceptionNames = [];

	/** @var list<string> */
	public $ignoredNames = [];

	/** @var bool */
	public $allowPartialUses = true;

	/**
	 * If empty, all namespaces are required to be used
	 *
	 * @var list<string>
	 */
	public $namespacesRequiredToUse = [];

	/** @var bool */
	public $allowFullyQualifiedNameForCollidingClasses = false;

	/** @var bool */
	public $allowFullyQualifiedNameForCollidingFunctions = false;

	/** @var bool */
	public $allowFullyQualifiedNameForCollidingConstants = false;

	/** @var list<string>|null */
	private $normalizedSpecialExceptionNames;

	/** @var list<string>|null */
	private $normalizedIgnoredNames;

	/** @var list<string>|null */
	private $normalizedNamespacesRequiredToUse;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
	{
		if (TokenHelper::findPrevious($phpcsFile, T_OPEN_TAG, $openTagPointer - 1) !== null) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$references = $this->getReferences($phpcsFile, $openTagPointer);

		$definedClassesIndex = [];
		foreach (ClassHelper::getAllNames($phpcsFile) as $definedClassPointer => $definedClassName) {
			$definedClassesIndex[strtolower($definedClassName)] = NamespaceHelper::resolveClassName(
				$phpcsFile,
				$definedClassName,
				$definedClassPointer
			);
		}
		$definedFunctionsIndex = array_flip(array_map(static function (string $functionName): string {
			return strtolower($functionName);
		}, FunctionHelper::getAllFunctionNames($phpcsFile)));
		$definedConstantsIndex = array_flip(ConstantHelper::getAllNames($phpcsFile));

		$classReferencesIndex = [];
		$classReferences = array_filter($references, static function (stdClass $reference): bool {
			return $reference->source === self::SOURCE_CODE && $reference->isClass;
		});

		foreach ($classReferences as $classReference) {
			$classReferencesIndex[strtolower($classReference->name)] = NamespaceHelper::resolveName(
				$phpcsFile,
				$classReference->name,
				$classReference->type,
				$classReference->startPointer
			);
		}

		$namespacePointers = NamespaceHelper::getAllNamespacesPointers($phpcsFile);

		$referenceErrors = [];

		foreach ($references as $reference) {
			$useStatements = UseStatementHelper::getUseStatementsForPointer($phpcsFile, $reference->startPointer);

			$name = $reference->name;
			/** @var int $startPointer */
			$startPointer = $reference->startPointer;
			$canonicalName = NamespaceHelper::normalizeToCanonicalName($name);
			$unqualifiedName = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($name);

			$collidingUseStatementUniqueId = UseStatement::getUniqueId($reference->type, $unqualifiedName);

			$isPartialUse = false;
			foreach ($useStatements as $useStatement) {
				$useStatementName = $useStatement->getAlias() ?? $useStatement->getNameAsReferencedInFile();
				if (strpos($name, $useStatementName . '\\') === 0) {
					$isPartialUse = true;
					break;
				}
			}

			$isFullyQualified = NamespaceHelper::isFullyQualifiedName($name)
				|| ($namespacePointers === [] && NamespaceHelper::hasNamespace($name) && !$isPartialUse);

			$isGlobalFallback = !$isFullyQualified
				&& !NamespaceHelper::hasNamespace($name)
				&& $namespacePointers !== []
				&& !array_key_exists(UseStatement::getUniqueId($reference->type, $name), $useStatements);

			$isGlobalFunctionFallback = false;
			if ($reference->isFunction && $isGlobalFallback) {
				$isGlobalFunctionFallback = !array_key_exists(strtolower($reference->name), $definedFunctionsIndex) && function_exists(
					$reference->name
				);
			}
			$isGlobalConstantFallback = false;
			if ($reference->isConstant && $isGlobalFallback) {
				$isGlobalConstantFallback = !array_key_exists($reference->name, $definedConstantsIndex) && defined($reference->name);
			}

			if ($isFullyQualified) {
				if ($reference->isClass && $this->allowFullyQualifiedNameForCollidingClasses) {
					$lowerCasedUnqualifiedClassName = strtolower($unqualifiedName);
					if (
						array_key_exists($lowerCasedUnqualifiedClassName, $definedClassesIndex)
						&& $canonicalName !== NamespaceHelper::normalizeToCanonicalName(
							$definedClassesIndex[$lowerCasedUnqualifiedClassName]
						)
					) {
						continue;
					}

					if (
						array_key_exists($lowerCasedUnqualifiedClassName, $classReferencesIndex)
						&& $name !== $classReferencesIndex[$lowerCasedUnqualifiedClassName]
					) {
						continue;
					}

					if (
						array_key_exists($collidingUseStatementUniqueId, $useStatements)
						&& $canonicalName !== NamespaceHelper::normalizeToCanonicalName(
							$useStatements[$collidingUseStatementUniqueId]->getFullyQualifiedTypeName()
						)
					) {
						continue;
					}
				} elseif ($reference->isFunction && $this->allowFullyQualifiedNameForCollidingFunctions) {
					$lowerCasedUnqualifiedFunctionName = strtolower($unqualifiedName);
					if (array_key_exists($lowerCasedUnqualifiedFunctionName, $definedFunctionsIndex)) {
						continue;
					}

					if (
						array_key_exists($collidingUseStatementUniqueId, $useStatements)
						&& $canonicalName !== NamespaceHelper::normalizeToCanonicalName(
							$useStatements[$collidingUseStatementUniqueId]->getFullyQualifiedTypeName()
						)
					) {
						continue;
					}
				} elseif ($reference->isConstant && $this->allowFullyQualifiedNameForCollidingConstants) {
					if (array_key_exists($unqualifiedName, $definedConstantsIndex)) {
						continue;
					}

					if (
						array_key_exists($collidingUseStatementUniqueId, $useStatements)
						&& $canonicalName !== NamespaceHelper::normalizeToCanonicalName(
							$useStatements[$collidingUseStatementUniqueId]->getFullyQualifiedTypeName()
						)
					) {
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

				if (
					$isFullyQualified
					&& !NamespaceHelper::hasNamespace($name)
					&& $namespacePointers === []
				) {
					$label = sprintf(
						$reference->isConstant ? 'Constant %s' : ($reference->isFunction ? 'Function %s()' : 'Class %s'),
						$name
					);

					$fix = $phpcsFile->addFixableError(sprintf(
						'%s should not be referenced via a fully qualified name, but via an unqualified name without the leading \\, because the file does not have a namespace and the type cannot be put in a use statement.',
						$label
					), $startPointer, self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE);
					if ($fix) {
						$phpcsFile->fixer->beginChangeset();

						if ($reference->source === self::SOURCE_ANNOTATION) {
							$fixedDocComment = AnnotationHelper::fixAnnotation(
								$reference->parsedDocComment,
								$reference->annotation,
								$reference->nameNode,
								new IdentifierTypeNode(substr($reference->name, 1))
							);

							FixerHelper::change(
								$phpcsFile,
								$reference->parsedDocComment->getOpenPointer(),
								$reference->parsedDocComment->getClosePointer(),
								$fixedDocComment
							);

						} elseif ($reference->source === self::SOURCE_ANNOTATION_CONSTANT_FETCH) {
							$fixedDocComment = AnnotationHelper::fixAnnotation(
								$reference->parsedDocComment,
								$reference->annotation,
								$reference->constantFetchNode,
								new ConstFetchNode(substr($reference->name, 1), $reference->constantFetchNode->name)
							);

							FixerHelper::change(
								$phpcsFile,
								$reference->parsedDocComment->getOpenPointer(),
								$reference->parsedDocComment->getClosePointer(),
								$fixedDocComment
							);
						} else {
							$phpcsFile->fixer->replaceToken($startPointer, substr($tokens[$startPointer]['content'], 1));
						}

						$phpcsFile->fixer->endChangeset();
					}
				} else {
					$shouldBeUsed = NamespaceHelper::hasNamespace($name);
					if (!$shouldBeUsed) {
						if ($reference->isFunction) {
							$shouldBeUsed = $isFullyQualified
								? !$this->allowFullyQualifiedGlobalFunctions
								: !$this->allowFallbackGlobalFunctions;
						} elseif ($reference->isConstant) {
							$shouldBeUsed = $isFullyQualified
								? !$this->allowFullyQualifiedGlobalConstants
								: !$this->allowFallbackGlobalConstants;
						} else {
							$shouldBeUsed = !$this->allowFullyQualifiedGlobalClasses;
						}
					}

					if (!$shouldBeUsed) {
						continue;
					}

					$referenceErrors[] = (object) [
						'reference' => $reference,
						'canonicalName' => $canonicalName,
						'isGlobalConstantFallback' => $isGlobalConstantFallback,
						'isGlobalFunctionFallback' => $isGlobalFunctionFallback,
					];
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

		if (count($referenceErrors) === 0) {
			return;
		}

		$alreadyAddedUses = [
			UseStatement::TYPE_CLASS => [],
			UseStatement::TYPE_FUNCTION => [],
			UseStatement::TYPE_CONSTANT => [],
		];

		$phpcsFile->fixer->beginChangeset();

		foreach ($referenceErrors as $referenceData) {
			$reference = $referenceData->reference;
			/** @var int $startPointer */
			$startPointer = $reference->startPointer;
			$canonicalName = $referenceData->canonicalName;
			$nameToReference = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($reference->name);
			$canonicalNameToReference = $reference->isConstant ? $nameToReference : strtolower($nameToReference);
			$isGlobalConstantFallback = $referenceData->isGlobalConstantFallback;
			$isGlobalFunctionFallback = $referenceData->isGlobalFunctionFallback;

			$useStatements = UseStatementHelper::getUseStatementsForPointer($phpcsFile, $reference->startPointer);

			$canBeFixed = array_reduce(
				$alreadyAddedUses[$reference->type],
				static function (bool $carry, string $use) use ($canonicalName): bool {
					$useLastName = strtolower(NamespaceHelper::getLastNamePart($use));
					$canonicalLastName = strtolower(NamespaceHelper::getLastNamePart($canonicalName));
					return $useLastName === $canonicalLastName ? false : $carry;
				},
				true
			);

			if (
				(
					$reference->isClass
					&& array_key_exists($canonicalNameToReference, $definedClassesIndex)
					&& $canonicalName !== NamespaceHelper::normalizeToCanonicalName($definedClassesIndex[$canonicalNameToReference])
				)
				|| (
					$reference->isClass
					&& array_key_exists($canonicalNameToReference, $classReferencesIndex)
					&& $canonicalName !== NamespaceHelper::normalizeToCanonicalName($classReferencesIndex[$canonicalNameToReference])
				)
				|| ($reference->isFunction && array_key_exists($canonicalNameToReference, $definedFunctionsIndex))
				|| ($reference->isConstant && array_key_exists($canonicalNameToReference, $definedConstantsIndex))
			) {
				$canBeFixed = false;
			}

			foreach ($useStatements as $useStatement) {
				if ($useStatement->getType() !== $reference->type) {
					continue;
				}

				if ($useStatement->getFullyQualifiedTypeName() === $canonicalName) {
					continue;
				}

				if ($useStatement->getCanonicalNameAsReferencedInFile() !== $canonicalNameToReference) {
					continue;
				}

				$canBeFixed = false;
				break;
			}

			$label = sprintf(
				$reference->isConstant
					? 'Constant %s'
					: ($reference->isFunction ? 'Function %s()' : 'Class %s'),
				$reference->name
			);
			$errorCode = $isGlobalConstantFallback || $isGlobalFunctionFallback
				? self::CODE_REFERENCE_VIA_FALLBACK_GLOBAL_NAME
				: self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME;
			$errorMessage = $isGlobalConstantFallback || $isGlobalFunctionFallback
				? sprintf('%s should not be referenced via a fallback global name, but via a use statement.', $label)
				: sprintf('%s should not be referenced via a fully qualified name, but via a use statement.', $label);

			if (!$canBeFixed) {
				$phpcsFile->addError($errorMessage, $startPointer, $errorCode);
				continue;
			}

			$fix = $phpcsFile->addFixableError($errorMessage, $startPointer, $errorCode);

			if (!$fix) {
				continue;
			}

			$addUse = !in_array($canonicalName, $alreadyAddedUses[$reference->type], true);

			if (
				$reference->isClass
				&& array_key_exists($canonicalNameToReference, $definedClassesIndex)
			) {
				$addUse = false;
			}

			foreach ($useStatements as $useStatement) {
				if (
					$useStatement->getType() !== $reference->type
					|| $useStatement->getFullyQualifiedTypeName() !== $canonicalName
				) {
					continue;
				}

				$nameToReference = $useStatement->getNameAsReferencedInFile();
				$addUse = false;
				// Lock the use statement, so it is not modified by other sniffs
				$phpcsFile->fixer->replaceToken(
					$useStatement->getPointer(),
					$phpcsFile->fixer->getTokenContent($useStatement->getPointer())
				);
				break;
			}

			if ($addUse) {
				$useStatementPlacePointer = $this->getUseStatementPlacePointer($phpcsFile, $openTagPointer, $useStatements);
				$useTypeName = UseStatement::getTypeName($reference->type);
				$useTypeFormatted = $useTypeName !== null ? sprintf('%s ', $useTypeName) : '';

				$phpcsFile->fixer->addNewline($useStatementPlacePointer);
				$phpcsFile->fixer->addContent($useStatementPlacePointer, sprintf('use %s%s;', $useTypeFormatted, $canonicalName));

				$alreadyAddedUses[$reference->type][] = $canonicalName;
			}

			if ($reference->source === self::SOURCE_ANNOTATION) {
				$fixedDocComment = AnnotationHelper::fixAnnotation(
					$reference->parsedDocComment,
					$reference->annotation,
					$reference->nameNode,
					new IdentifierTypeNode($nameToReference)
				);

				FixerHelper::change(
					$phpcsFile,
					$reference->parsedDocComment->getOpenPointer(),
					$reference->parsedDocComment->getClosePointer(),
					$fixedDocComment
				);

			} elseif ($reference->source === self::SOURCE_ANNOTATION_CONSTANT_FETCH) {
				$fixedDocComment = AnnotationHelper::fixAnnotation(
					$reference->parsedDocComment,
					$reference->annotation,
					$reference->constantFetchNode,
					new ConstFetchNode($nameToReference, $reference->constantFetchNode->name)
				);

				FixerHelper::change(
					$phpcsFile,
					$reference->parsedDocComment->getOpenPointer(),
					$reference->parsedDocComment->getClosePointer(),
					$fixedDocComment
				);

			} elseif ($reference->source === self::SOURCE_ATTRIBUTE) {
				$attributeContent = TokenHelper::getContent($phpcsFile, $startPointer, $reference->endPointer);
				$fixedAttributeContent = preg_replace(
					'~(?<=\W)' . preg_quote($reference->name, '~') . '(?=\W)~',
					$nameToReference,
					$attributeContent
				);
				FixerHelper::change($phpcsFile, $startPointer, $reference->endPointer, $fixedAttributeContent);
			} else {
				FixerHelper::change($phpcsFile, $startPointer, $reference->endPointer, $nameToReference);
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @return list<string>
	 */
	private function getSpecialExceptionNames(): array
	{
		if ($this->normalizedSpecialExceptionNames === null) {
			$this->normalizedSpecialExceptionNames = SniffSettingsHelper::normalizeArray($this->specialExceptionNames);
		}

		return $this->normalizedSpecialExceptionNames;
	}

	/**
	 * @return list<string>
	 */
	private function getIgnoredNames(): array
	{
		if ($this->normalizedIgnoredNames === null) {
			$this->normalizedIgnoredNames = SniffSettingsHelper::normalizeArray($this->ignoredNames);
		}

		return $this->normalizedIgnoredNames;
	}

	/**
	 * @return list<string>
	 */
	private function getNamespacesRequiredToUse(): array
	{
		if ($this->normalizedNamespacesRequiredToUse === null) {
			$this->normalizedNamespacesRequiredToUse = SniffSettingsHelper::normalizeArray($this->namespacesRequiredToUse);
		}

		return $this->normalizedNamespacesRequiredToUse;
	}

	/**
	 * @param array<string, UseStatement> $useStatements
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

		$tokens = $phpcsFile->getTokens();

		$useStatementPlacePointer = $openTagPointer;

		$nonWhitespacePointerAfterOpenTag = TokenHelper::findNextNonWhitespace($phpcsFile, $openTagPointer + 1);
		if (in_array($tokens[$nonWhitespacePointerAfterOpenTag]['code'], Tokens::$commentTokens, true)) {
			$commentEndPointer = CommentHelper::getCommentEndPointer($phpcsFile, $nonWhitespacePointerAfterOpenTag);

			if (StringHelper::endsWith($tokens[$commentEndPointer]['content'], $phpcsFile->eolChar)) {
				$useStatementPlacePointer = $commentEndPointer;
			} else {
				$newLineAfterComment = $commentEndPointer + 1;

				if (array_key_exists($newLineAfterComment, $tokens) && $tokens[$newLineAfterComment]['content'] === $phpcsFile->eolChar) {
					$pointerAfterCommentEnd = TokenHelper::findNextNonWhitespace($phpcsFile, $newLineAfterComment + 1);

					if (TokenHelper::findNextContent(
						$phpcsFile,
						T_WHITESPACE,
						$phpcsFile->eolChar,
						$newLineAfterComment + 1,
						$pointerAfterCommentEnd
					) !== null) {
						$useStatementPlacePointer = $commentEndPointer;
					}
				}
			}
		}

		$pointerAfter = TokenHelper::findNextEffective($phpcsFile, $useStatementPlacePointer + 1);
		if ($tokens[$pointerAfter]['code'] === T_DECLARE) {
			return TokenHelper::findNext($phpcsFile, T_SEMICOLON, $pointerAfter + 1);
		}

		return $useStatementPlacePointer;
	}

	private function isRequiredToBeUsed(string $name): bool
	{
		if ($this->namespacesRequiredToUse === null || $this->namespacesRequiredToUse === []) {
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
	 * @return list<stdClass>
	 */
	private function getReferences(File $phpcsFile, int $openTagPointer): array
	{
		$tokens = $phpcsFile->getTokens();

		$references = [];
		foreach (ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer) as $referencedName) {
			$reference = new stdClass();
			$reference->source = self::SOURCE_CODE;
			$reference->name = $referencedName->getNameAsReferencedInFile();
			$reference->type = $referencedName->getType();
			$reference->startPointer = $referencedName->getStartPointer();
			$reference->endPointer = $referencedName->getEndPointer();
			$reference->isClass = $referencedName->isClass();
			$reference->isConstant = $referencedName->isConstant();
			$reference->isFunction = $referencedName->isFunction();

			$references[] = $reference;
		}

		foreach (ReferencedNameHelper::getAllReferencedNamesInAttributes($phpcsFile, $openTagPointer) as $referencedName) {
			$reference = new stdClass();
			$reference->source = self::SOURCE_ATTRIBUTE;
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

			$parsedDocComment = DocCommentHelper::parseDocComment($phpcsFile, $docCommentOpenPointer);

			if ($parsedDocComment !== null) {
				$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);

				foreach ($annotations as $annotation) {
					/** @var list<IdentifierTypeNode> $identifierTypeNodes */
					$identifierTypeNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), IdentifierTypeNode::class);

					foreach ($identifierTypeNodes as $typeHintNode) {
						$typeHint = $typeHintNode->name;

						$lowercasedTypeHint = strtolower($typeHint);
						if (
							TypeHintHelper::isSimpleTypeHint($lowercasedTypeHint)
							|| TypeHintHelper::isSimpleUnofficialTypeHints($lowercasedTypeHint)
							|| !TypeHelper::isTypeName($typeHint)
						) {
							continue;
						}

						$reference = new stdClass();
						$reference->source = self::SOURCE_ANNOTATION;
						$reference->parsedDocComment = $parsedDocComment;
						$reference->annotation = $annotation;
						$reference->nameNode = $typeHintNode;
						$reference->name = $typeHint;
						$reference->type = ReferencedName::TYPE_CLASS;
						$reference->startPointer = $annotation->getStartPointer();
						$reference->endPointer = null;
						$reference->isClass = true;
						$reference->isConstant = false;
						$reference->isFunction = false;

						$references[] = $reference;
					}

					/** @var list<ConstFetchNode> $constantFetchNodes */
					$constantFetchNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), ConstFetchNode::class);

					foreach ($constantFetchNodes as $constantFetchNode) {
						$reference = new stdClass();
						$reference->source = self::SOURCE_ANNOTATION_CONSTANT_FETCH;
						$reference->parsedDocComment = $parsedDocComment;
						$reference->annotation = $annotation;
						$reference->constantFetchNode = $constantFetchNode;
						$reference->name = $constantFetchNode->className;
						$reference->type = ReferencedName::TYPE_CLASS;
						$reference->startPointer = $annotation->getStartPointer();
						$reference->endPointer = null;
						$reference->isClass = true;
						$reference->isConstant = false;
						$reference->isFunction = false;

						$references[] = $reference;
					}
				}
			}

			$searchAnnotationsPointer = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
		}

		return $references;
	}

}
