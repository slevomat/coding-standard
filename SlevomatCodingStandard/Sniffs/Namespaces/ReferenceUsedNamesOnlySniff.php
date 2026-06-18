<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PHPStan\PhpDocParser\Ast\ConstExpr\ConstFetchNode;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\CommentHelper;
use SlevomatCodingStandard\Helpers\ConstantHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ParsedDocComment;
use SlevomatCodingStandard\Helpers\ReferencedName;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function array_flip;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_reduce;
use function array_values;
use function count;
use function defined;
use function function_exists;
use function implode;
use function in_array;
use function preg_quote;
use function preg_replace;
use function sprintf;
use function strlen;
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

	public bool $searchAnnotations = false;

	public bool $allowFullyQualifiedExceptions = false;

	public bool $allowFullyQualifiedGlobalClasses = false;

	public bool $allowFullyQualifiedGlobalFunctions = false;

	public bool $allowFallbackGlobalFunctions = true;

	public bool $allowFullyQualifiedGlobalConstants = false;

	public bool $allowFallbackGlobalConstants = true;

	public bool $allowWhenNoNamespace = true;

	/** @var list<string> */
	public array $specialExceptionNames = [];

	/** @var list<string> */
	public array $ignoredNames = [];

	public bool $allowPartialUses = true;

	/**
	 * If empty, all partial uses are allowed.
	 *
	 * @var list<string>
	 */
	public array $namespacesAllowedToUsePartially = [];

	/**
	 * If empty, no namespace is required to be used partially.
	 *
	 * @var list<string>
	 */
	public array $namespacesRequiredToUsePartially = [];

	/**
	 * If empty, all namespaces are required to be used
	 *
	 * @var list<string>
	 */
	public array $namespacesRequiredToUse = [];

	public bool $allowFullyQualifiedNameForCollidingClasses = false;

	public bool $allowFullyQualifiedNameForCollidingFunctions = false;

	public bool $allowFullyQualifiedNameForCollidingConstants = false;

	/** @var list<string>|null */
	private ?array $normalizedSpecialExceptionNames = null;

	/** @var list<string>|null */
	private ?array $normalizedIgnoredNames = null;

	/** @var list<string>|null */
	private ?array $normalizedNamespacesRequiredToUse = null;

	/** @var array<string, string>|null */
	private ?array $normalizedNamespacesAllowedToUsePartially = null;

	/** @var array<string, string>|null */
	private ?array $normalizedNamespacesRequiredToUsePartially = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	public function process(File $phpcsFile, int $openTagPointer): void
	{
		if (TokenHelper::findPrevious($phpcsFile, T_OPEN_TAG, $openTagPointer - 1) !== null) {
			return;
		}

		$namespacePointers = NamespaceHelper::getAllNamespacesPointers($phpcsFile);

		if ($namespacePointers === [] && !$this->allowWhenNoNamespace) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$references = $this->getReferences($phpcsFile, $openTagPointer);

		$definedClassesIndex = [];
		foreach (ClassHelper::getAllNames($phpcsFile) as $definedClassPointer => $definedClassName) {
			$definedClassesIndex[strtolower($definedClassName)] = NamespaceHelper::resolveClassName(
				$phpcsFile,
				$definedClassName,
				$definedClassPointer,
			);
		}
		$definedFunctionsIndex = array_flip(
			array_map(
				static fn (string $functionName): string => strtolower($functionName),
				FunctionHelper::getAllFunctionNames($phpcsFile),
			),
		);
		$definedConstantsIndex = array_flip(ConstantHelper::getAllNames($phpcsFile));

		$classReferencesIndex = [];
		foreach ($references as [$classReference, $source]) {
			if ($source !== self::SOURCE_CODE || !$classReference->isClass()) {
				continue;
			}

			$classReferencesIndex[strtolower($classReference->getNameAsReferencedInFile())] = NamespaceHelper::resolveName(
				$phpcsFile,
				$classReference->getNameAsReferencedInFile(),
				$classReference->getType(),
				$classReference->getStartPointer(),
			);
		}

		$referenceErrors = [];

		foreach ($references as [$reference, $source, $parsedDocComment, $annotation, $nameNode, $constantFetchNode]) {
			$useStatements = UseStatementHelper::getUseStatementsForPointer($phpcsFile, $reference->getStartPointer());

			$name = $reference->getNameAsReferencedInFile();
			/** @var int $startPointer */
			$startPointer = $reference->getStartPointer();
			$canonicalName = NamespaceHelper::normalizeToCanonicalName($name);
			$unqualifiedName = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($name);

			if (in_array(strtolower($unqualifiedName), ['true', 'false', 'null'], true)) {
				continue;
			}

			$collidingUseStatementUniqueId = UseStatement::getUniqueId($reference->getType(), $unqualifiedName);

			$partialUse = $this->getPartialUse($name, $useStatements);
			$isPartialUse = $partialUse !== null;

			$isFullyQualified = NamespaceHelper::isFullyQualifiedName($name)
				|| ($namespacePointers === [] && NamespaceHelper::hasNamespace($name) && !$isPartialUse);

			$isGlobalFallback = !$isFullyQualified
				&& !NamespaceHelper::hasNamespace($name)
				&& $namespacePointers !== []
				&& !array_key_exists(UseStatement::getUniqueId($reference->getType(), $name), $useStatements);

			$isGlobalFunctionFallback = false;
			if ($reference->isFunction() && $isGlobalFallback) {
				$isGlobalFunctionFallback = !array_key_exists(
					strtolower($reference->getNameAsReferencedInFile()),
					$definedFunctionsIndex,
				) && function_exists(
					$reference->getNameAsReferencedInFile(),
				);
			}
			$isGlobalConstantFallback = false;
			if ($reference->isConstant() && $isGlobalFallback) {
				$isGlobalConstantFallback = !array_key_exists($reference->getNameAsReferencedInFile(), $definedConstantsIndex) && defined(
					$reference->getNameAsReferencedInFile(),
				);
			}

			if ($isFullyQualified) {
				$hasExistingUseForCanonicalName = false;
				foreach ($useStatements as $useStatement) {
					if (
						$useStatement->getType() === $reference->getType()
						&& $useStatement->getFullyQualifiedTypeName() === $canonicalName
					) {
						$hasExistingUseForCanonicalName = true;
						break;
					}
				}

				if (!$hasExistingUseForCanonicalName) {
					if ($reference->isClass() && $this->allowFullyQualifiedNameForCollidingClasses) {
						$lowerCasedUnqualifiedClassName = strtolower($unqualifiedName);
						if (
						array_key_exists($lowerCasedUnqualifiedClassName, $definedClassesIndex)
						&& $canonicalName !== NamespaceHelper::normalizeToCanonicalName(
							$definedClassesIndex[$lowerCasedUnqualifiedClassName],
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
							$useStatements[$collidingUseStatementUniqueId]->getFullyQualifiedTypeName(),
						)
						) {
							continue;
						}
					} elseif ($reference->isFunction() && $this->allowFullyQualifiedNameForCollidingFunctions) {
						$lowerCasedUnqualifiedFunctionName = strtolower($unqualifiedName);
						if (array_key_exists($lowerCasedUnqualifiedFunctionName, $definedFunctionsIndex)) {
							continue;
						}

						if (
						array_key_exists($collidingUseStatementUniqueId, $useStatements)
						&& $canonicalName !== NamespaceHelper::normalizeToCanonicalName(
							$useStatements[$collidingUseStatementUniqueId]->getFullyQualifiedTypeName(),
						)
						) {
							continue;
						}
					} elseif ($reference->isConstant() && $this->allowFullyQualifiedNameForCollidingConstants) {
						if (array_key_exists($unqualifiedName, $definedConstantsIndex)) {
							continue;
						}

						if (
						array_key_exists($collidingUseStatementUniqueId, $useStatements)
						&& $canonicalName !== NamespaceHelper::normalizeToCanonicalName(
							$useStatements[$collidingUseStatementUniqueId]->getFullyQualifiedTypeName(),
						)
						) {
							continue;
						}
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
						$reference->isConstant() ? 'Constant %s' : ($reference->isFunction() ? 'Function %s()' : 'Class %s'),
						$name,
					);

					$fix = $phpcsFile->addFixableError(sprintf(
						'%s should not be referenced via a fully qualified name, but via an unqualified name without the leading \\, because the file does not have a namespace and the type cannot be put in a use statement.',
						$label,
					), $startPointer, self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE);
					if ($fix) {
						$phpcsFile->fixer->beginChangeset();

						if ($source === self::SOURCE_ANNOTATION) {
							$fixedDocComment = AnnotationHelper::fixAnnotation(
								$parsedDocComment,
								$annotation,
								$nameNode,
								new IdentifierTypeNode(substr($reference->getNameAsReferencedInFile(), 1)),
							);

							FixerHelper::change(
								$phpcsFile,
								$parsedDocComment->getOpenPointer(),
								$parsedDocComment->getClosePointer(),
								$fixedDocComment,
							);

						} elseif ($source === self::SOURCE_ANNOTATION_CONSTANT_FETCH) {
							$fixedDocComment = AnnotationHelper::fixAnnotation(
								$parsedDocComment,
								$annotation,
								$constantFetchNode,
								new ConstFetchNode(substr($reference->getNameAsReferencedInFile(), 1), $constantFetchNode->name),
							);

							FixerHelper::change(
								$phpcsFile,
								$parsedDocComment->getOpenPointer(),
								$parsedDocComment->getClosePointer(),
								$fixedDocComment,
							);
						} else {
							FixerHelper::replace(
								$phpcsFile,
								$startPointer,
								substr($tokens[$startPointer]['content'], 1),
							);
						}

						$phpcsFile->fixer->endChangeset();
					}
				} else {
					$shouldBeUsed = NamespaceHelper::hasNamespace($name);
					if (!$shouldBeUsed) {
						if ($reference->isFunction()) {
							$shouldBeUsed = $isFullyQualified
								? !$this->allowFullyQualifiedGlobalFunctions
								: !$this->allowFallbackGlobalFunctions;
						} elseif ($reference->isConstant()) {
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

					$referenceErrors[] = [
						$reference,
						$canonicalName,
						$isGlobalConstantFallback,
						$isGlobalFunctionFallback,
						$source,
						$parsedDocComment,
						$annotation,
						$nameNode,
						$constantFetchNode,
					];
				}
			} elseif (
				NamespaceHelper::isQualifiedName($name)
				&& (
					$partialUse !== null
						? !$this->isPartialUseAllowed($partialUse)
						: !$this->allowPartialUses
				)
			) {
				$partialUseMessage = 'Partial use statements are not allowed';
				$allowedNamespaces = $this->getNamespacesAllowedToUsePartially();
				if ($allowedNamespaces !== []) {
					$partialUseMessage .= sprintf(' except for %s', implode(', ', $this->formatPartialUseNamespaces($allowedNamespaces)));
				}

				$phpcsFile->addError(sprintf(
					'%s, but referencing %s found.',
					$partialUseMessage,
					$name,
				), $startPointer, self::CODE_PARTIAL_USE);
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

		foreach ($referenceErrors as [$reference, $canonicalName, $isGlobalConstantFallback, $isGlobalFunctionFallback, $source, $parsedDocComment, $annotation, $nameNode, $constantFetchNode]) {
			/** @var int $startPointer */
			$startPointer = $reference->getStartPointer();
			$requiredPartialUse = $this->getRequiredPartialUse($canonicalName);
			$requiredPartialNamespace = $requiredPartialUse !== null ? $requiredPartialUse['namespace'] : null;
			$canonicalNameToUse = $requiredPartialNamespace ?? $canonicalName;
			$nameToReference = $requiredPartialUse !== null
				? $this->getPartialUseName($requiredPartialUse['namespace'], $requiredPartialUse['alias'])
				: NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($canonicalNameToUse);
			$referenceNameSuffix = $requiredPartialUse !== null && $canonicalName !== $requiredPartialNamespace
				? sprintf('\\%s', substr($canonicalName, strlen($requiredPartialNamespace) + 1))
				: '';
			$referenceNameToReplace = $requiredPartialUse !== null
				? sprintf('%s%s', $nameToReference, $referenceNameSuffix)
				: $nameToReference;
			$canonicalNameToReference = $reference->isConstant() ? $referenceNameToReplace : strtolower($referenceNameToReplace);

			$useStatements = UseStatementHelper::getUseStatementsForPointer($phpcsFile, $reference->getStartPointer());

			$canBeFixed = array_reduce(
				$alreadyAddedUses[$reference->getType()],
				static function (bool $carry, string $use) use ($canonicalNameToUse): bool {
					$useLastName = strtolower(NamespaceHelper::getLastNamePart($use));
					$canonicalLastName = strtolower(NamespaceHelper::getLastNamePart($canonicalNameToUse));
					return $useLastName === $canonicalLastName ? false : $carry;
				},
				true,
			);

			$hasExistingUseForCanonicalName = false;
			$hasCollision = false;
			foreach ($useStatements as $useStatement) {
				if ($useStatement->getType() !== $reference->getType()) {
					continue;
				}

				if ($useStatement->getFullyQualifiedTypeName() === $canonicalNameToUse) {
					$hasExistingUseForCanonicalName = true;
					break;
				}

				if ($useStatement->getCanonicalNameAsReferencedInFile() === $canonicalNameToReference) {
					$hasCollision = true;
				}
			}

			if (!$hasExistingUseForCanonicalName) {
				if (
					(
						$reference->isClass()
						&& array_key_exists($canonicalNameToReference, $definedClassesIndex)
						&& $canonicalNameToUse !== NamespaceHelper::normalizeToCanonicalName(
							$definedClassesIndex[$canonicalNameToReference],
						)
					)
					|| (
						$reference->isClass()
						&& array_key_exists($canonicalNameToReference, $classReferencesIndex)
						&& $canonicalNameToUse !== NamespaceHelper::normalizeToCanonicalName(
							$classReferencesIndex[$canonicalNameToReference],
						)
					)
						|| ($reference->isFunction() && array_key_exists($canonicalNameToReference, $definedFunctionsIndex))
						|| ($reference->isConstant() && array_key_exists($canonicalNameToReference, $definedConstantsIndex))
				) {
					$canBeFixed = false;
				}

				if ($hasCollision) {
					$canBeFixed = false;
				}
			}

			$label = sprintf(
				$reference->isConstant()
					? 'Constant %s'
					: ($reference->isFunction() ? 'Function %s()' : 'Class %s'),
				$reference->getNameAsReferencedInFile(),
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

			$addUse = !in_array($canonicalNameToUse, $alreadyAddedUses[$reference->getType()], true);

			if (
				$reference->isClass()
				&& array_key_exists($canonicalNameToReference, $definedClassesIndex)
			) {
				$addUse = false;
			}

			foreach ($useStatements as $useStatement) {
				if (
					$useStatement->getType() !== $reference->getType()
					|| $useStatement->getFullyQualifiedTypeName() !== $canonicalNameToUse
				) {
					continue;
				}

				$nameToReference = $useStatement->getNameAsReferencedInFile();
				$referenceNameToReplace = sprintf('%s%s', $nameToReference, $referenceNameSuffix);
				$addUse = false;
				// Lock the use statement, so it is not modified by other sniffs
				FixerHelper::replace(
					$phpcsFile,
					$useStatement->getPointer(),
					$phpcsFile->fixer->getTokenContent($useStatement->getPointer()),
				);
				break;
			}

			if ($addUse) {
				$useStatementPlacePointer = $this->getUseStatementPlacePointer($phpcsFile, $openTagPointer, $useStatements);
				$useTypeName = UseStatement::getTypeName($reference->getType());
				$useTypeFormatted = $useTypeName !== null ? sprintf('%s ', $useTypeName) : '';

				$phpcsFile->fixer->addNewline($useStatementPlacePointer);
				FixerHelper::add(
					$phpcsFile,
					$useStatementPlacePointer,
					sprintf(
						'use %s%s%s;',
						$useTypeFormatted,
						$canonicalNameToUse,
						$requiredPartialUse !== null && $requiredPartialUse['alias'] !== ''
							? sprintf(' as %s', $requiredPartialUse['alias'])
							: '',
					),
				);

				$alreadyAddedUses[$reference->getType()][] = $canonicalNameToUse;
			}

			if ($source === self::SOURCE_ANNOTATION) {
				$fixedDocComment = AnnotationHelper::fixAnnotation(
					$parsedDocComment,
					$annotation,
					$nameNode,
					new IdentifierTypeNode($referenceNameToReplace),
				);

				FixerHelper::change(
					$phpcsFile,
					$parsedDocComment->getOpenPointer(),
					$parsedDocComment->getClosePointer(),
					$fixedDocComment,
				);

			} elseif ($source === self::SOURCE_ANNOTATION_CONSTANT_FETCH) {
				$fixedDocComment = AnnotationHelper::fixAnnotation(
					$parsedDocComment,
					$annotation,
					$constantFetchNode,
					new ConstFetchNode($referenceNameToReplace, $constantFetchNode->name),
				);

				FixerHelper::change(
					$phpcsFile,
					$parsedDocComment->getOpenPointer(),
					$parsedDocComment->getClosePointer(),
					$fixedDocComment,
				);

			} elseif ($source === self::SOURCE_ATTRIBUTE) {
				$attributeContent = TokenHelper::getContent($phpcsFile, $startPointer, $reference->getEndPointer());
				$fixedAttributeContent = preg_replace(
					'~(?<=\W)' . preg_quote($reference->getNameAsReferencedInFile(), '~') . '(?=\W)~',
					$referenceNameToReplace,
					$attributeContent,
				);
				FixerHelper::change($phpcsFile, $startPointer, $reference->getEndPointer(), $fixedAttributeContent);
			} else {
				FixerHelper::change($phpcsFile, $startPointer, $reference->getEndPointer(), $referenceNameToReplace);
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @return list<string>
	 */
	private function getSpecialExceptionNames(): array
	{
		$this->normalizedSpecialExceptionNames ??= SniffSettingsHelper::normalizeArray($this->specialExceptionNames);

		return $this->normalizedSpecialExceptionNames;
	}

	/**
	 * @return list<string>
	 */
	private function getIgnoredNames(): array
	{
		$this->normalizedIgnoredNames ??= SniffSettingsHelper::normalizeArray($this->ignoredNames);

		return $this->normalizedIgnoredNames;
	}

	/**
	 * @return list<string>
	 */
	private function getNamespacesRequiredToUse(): array
	{
		$this->normalizedNamespacesRequiredToUse ??= SniffSettingsHelper::normalizeArray($this->namespacesRequiredToUse);

		return $this->normalizedNamespacesRequiredToUse;
	}

	/**
	 * @return array<string, string>
	 */
	private function getNamespacesAllowedToUsePartially(): array
	{
		$this->normalizedNamespacesAllowedToUsePartially ??= $this->normalizePartialUseNamespaces($this->namespacesAllowedToUsePartially);

		return $this->normalizedNamespacesAllowedToUsePartially;
	}

	/**
	 * @return array<string, string>
	 */
	private function getNamespacesRequiredToUsePartially(): array
	{
		$this->normalizedNamespacesRequiredToUsePartially ??= $this->normalizePartialUseNamespaces($this->namespacesRequiredToUsePartially);

		return $this->normalizedNamespacesRequiredToUsePartially;
	}

	/**
	 * @param list<string> $settings
	 * @return array<string, string>
	 */
	private function normalizePartialUseNamespaces(array $settings): array
	{
		$normalizedSettings = [];
		foreach (SniffSettingsHelper::normalizeArray($settings) as $setting) {
			$parsedSetting = NamespaceHelper::parseNamespaceWithAlias($setting);
			$namespace = $parsedSetting['namespace'];
			$alias = $parsedSetting['alias'];

			$normalizedSettings[NamespaceHelper::normalizeToCanonicalName($namespace)] = $alias;
		}

		return $normalizedSettings;
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
		if (
			substr($tokens[$openTagPointer]['content'], -1) !== $phpcsFile->eolChar
			&& $tokens[$openTagPointer + 1]['content'] === $phpcsFile->eolChar
		) {
			// @codeCoverageIgnoreStart
			$useStatementPlacePointer++;
			// @codeCoverageIgnoreEnd
		}

		$nonWhitespacePointerAfterOpenTag = TokenHelper::findNextNonWhitespace($phpcsFile, $openTagPointer + 1);
		if (in_array($tokens[$nonWhitespacePointerAfterOpenTag]['code'], Tokens::COMMENT_TOKENS, true)) {
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
						$pointerAfterCommentEnd,
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
		$canonicalName = NamespaceHelper::normalizeToCanonicalName($name);

		foreach ($this->getNamespacesRequiredToUse() as $namespace) {
			if (!NamespaceHelper::isTypeInNamespace($name, $namespace)) {
				continue;
			}

			return true;
		}

		foreach (array_keys($this->getNamespacesRequiredToUsePartially()) as $namespace) {
			if ($canonicalName !== $namespace) {
				continue;
			}

			return true;
		}

		return $this->namespacesRequiredToUse === [];
	}

	/**
	 * @return array{namespace: string, alias: string}|null
	 */
	private function getRequiredPartialUse(string $name): ?array
	{
		foreach ($this->getNamespacesRequiredToUsePartially() as $namespace => $alias) {
			if (!NamespaceHelper::isTypeInNamespace($name, $namespace)) {
				continue;
			}

			return [
				'namespace' => $namespace,
				'alias' => $alias,
			];
		}

		return null;
	}

	/**
	 * @param array<string, UseStatement> $useStatements
	 * @return array{namespace: string, usedName: string}|null
	 */
	private function getPartialUse(string $name, array $useStatements): ?array
	{
		foreach ($useStatements as $useStatement) {
			$useStatementName = $useStatement->getAlias() ?? $useStatement->getNameAsReferencedInFile();
			if (!StringHelper::startsWith($name, $useStatementName . '\\')) {
				continue;
			}

			return [
				'namespace' => NamespaceHelper::normalizeToCanonicalName($useStatement->getFullyQualifiedTypeName()),
				'usedName' => $useStatementName,
			];
		}

		return null;
	}

	/**
	 * @param array{namespace: string, usedName: string} $partialUse
	 */
	private function isPartialUseAllowed(array $partialUse): bool
	{
		foreach ($this->getNamespacesRequiredToUsePartially() as $namespace => $alias) {
			if ($partialUse['namespace'] !== $namespace) {
				continue;
			}

			return $alias === '' || $alias === $partialUse['usedName'];
		}

		$allowedNamespaces = $this->getNamespacesAllowedToUsePartially();
		if ($allowedNamespaces !== []) {
			foreach ($allowedNamespaces as $namespace => $alias) {
				if ($partialUse['namespace'] !== $namespace) {
					continue;
				}

				return $alias === '' || $alias === $partialUse['usedName'];
			}

			return false;
		}

		return $this->allowPartialUses;
	}

	private function getPartialUseName(string $namespace, string $alias): string
	{
		if ($alias !== '') {
			return $alias;
		}

		return NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($namespace);
	}

	/**
	 * @param array<string, string> $namespaces
	 * @return list<string>
	 */
	private function formatPartialUseNamespaces(array $namespaces): array
	{
		$formattedNamespaces = [];
		foreach ($namespaces as $namespace => $alias) {
			$formattedNamespaces[] = $alias !== ''
				? sprintf('%s as %s', $namespace, $alias)
				: $namespace;
		}

		return $formattedNamespaces;
	}

	/**
	 * @return list<array{0: ReferencedName, 1: int, 2: ParsedDocComment|null, 3: Annotation<PhpDocTagValueNode>|null, 4: Node|null, 5: ConstFetchNode|null}>
	 */
	private function getReferences(File $phpcsFile, int $openTagPointer): array
	{
		$tokens = $phpcsFile->getTokens();

		$references = [];
		foreach (ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer) as $referencedName) {
			$references[] = [$referencedName, self::SOURCE_CODE, null, null, null, null];
		}

		foreach (ReferencedNameHelper::getAllReferencedNamesInAttributes($phpcsFile, $openTagPointer) as $referencedName) {
			$references[] = [$referencedName, self::SOURCE_ATTRIBUTE, null, null, null, null];
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

						$references[] = [
							new ReferencedName(
								$typeHint,
								$annotation->getStartPointer(),
								$annotation->getEndPointer(),
								ReferencedName::TYPE_CLASS,
							),
							self::SOURCE_ANNOTATION,
							$parsedDocComment,
							$annotation,
							$typeHintNode, null];
					}

					$constantFetchNodes = AnnotationHelper::getAnnotationNodesByType($annotation->getNode(), ConstFetchNode::class);

					foreach ($constantFetchNodes as $constantFetchNode) {
						$references[] = [
							new ReferencedName(
								$constantFetchNode->className,
								$annotation->getStartPointer(),
								$annotation->getEndPointer(),
								ReferencedName::TYPE_CLASS,
							),
							self::SOURCE_ANNOTATION_CONSTANT_FETCH,
							$parsedDocComment,
							$annotation,
							null, $constantFetchNode];
					}
				}
			}

			$searchAnnotationsPointer = $tokens[$docCommentOpenPointer]['comment_closer'] + 1;
		}

		return $references;
	}

}
