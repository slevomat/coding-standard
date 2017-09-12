<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedName;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class ReferenceUsedNamesOnlySniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME = 'ReferenceViaFullyQualifiedName';

	public const CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE = 'ReferenceViaFullyQualifiedNameWithoutNamespace';

	public const CODE_PARTIAL_USE = 'PartialUse';

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
	public $allowFullyQualifiedGlobalConstants = false;

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

	/**
	 * @return mixed[]
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
					throw new \SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException($keyword);
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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $openTagPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
		$definedClassesIndex = array_flip(array_map(function (string $className): string {
			return strtolower($className);
		}, ClassHelper::getAllNames($phpcsFile)));

		if ($this->allowFullyQualifiedNameForCollidingClasses) {
			$referencesIndex = array_flip(
				array_map(
					function (ReferencedName $referencedName): string {
						return strtolower($referencedName->getNameAsReferencedInFile());
					},
					$referencedNames
				)
			);
		}

		foreach ($referencedNames as $referencedName) {
			$name = $referencedName->getNameAsReferencedInFile();
			$nameStartPointer = $referencedName->getStartPointer();
			$canonicalName = NamespaceHelper::normalizeToCanonicalName($name);

			if ($this->allowFullyQualifiedNameForCollidingClasses) {
				$unqualifiedClassName = strtolower(NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($name));
				if (isset($referencesIndex[$unqualifiedClassName]) || array_key_exists($unqualifiedClassName, $definedClassesIndex ?? [])) {
					continue;
				}
			}

			if (NamespaceHelper::isFullyQualifiedName($name)) {
				if (!$this->isClassRequiredToBeUsed($name)) {
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

				$previousKeywordPointer = TokenHelper::findPreviousExcluding($phpcsFile, array_merge(TokenHelper::$nameTokenCodes, [T_WHITESPACE, T_COMMA]), $nameStartPointer - 1);
				if (!in_array($tokens[$previousKeywordPointer]['code'], $this->getFullyQualifiedKeywords(), true)) {
					if (
						!NamespaceHelper::hasNamespace($name)
						&& NamespaceHelper::findCurrentNamespaceName($phpcsFile, $nameStartPointer) === null
					) {
						$fix = $phpcsFile->addFixableError(sprintf(
							'Type %s should not be referenced via a fully qualified name, but via an unqualified name without the leading \\, because the file does not have a namespace and the type cannot be put in a use statement.',
							$name
						), $nameStartPointer, self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME_WITHOUT_NAMESPACE);
						if ($fix) {
							$phpcsFile->fixer->beginChangeset();
							$phpcsFile->fixer->replaceToken($nameStartPointer, substr($tokens[$nameStartPointer]['content'], 1));
							$phpcsFile->fixer->endChangeset();
						}
					} else {
						$shouldBeUsed = NamespaceHelper::hasNamespace($name);
						if (!$shouldBeUsed) {
							if ($referencedName->isFunction()) {
								$shouldBeUsed = !$this->allowFullyQualifiedGlobalFunctions;
							} elseif ($referencedName->isConstant()) {
								$shouldBeUsed = !$this->allowFullyQualifiedGlobalConstants;
							} else {
								$shouldBeUsed = !$this->allowFullyQualifiedGlobalClasses;
							}
						}

						if (!$shouldBeUsed) {
							continue;
						}

						$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);
						$nameToReference = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($name);
						$canonicalNameToReference = strtolower($nameToReference);

						$canBeFixed = true;
						foreach ($useStatements as $useStatement) {
							if ($useStatement->getFullyQualifiedTypeName() !== $canonicalName
								&& ($useStatement->getCanonicalNameAsReferencedInFile() === $canonicalNameToReference || array_key_exists($canonicalNameToReference, $definedClassesIndex))
							) {
								$canBeFixed = false;
								break;
							}
						}

						$errorMessage = sprintf('Type %s should not be referenced via a fully qualified name, but via a use statement.', $name);
						if ($canBeFixed) {
							$fix = $phpcsFile->addFixableError($errorMessage, $nameStartPointer, self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
						} else {
							$phpcsFile->addError($errorMessage, $nameStartPointer, self::CODE_REFERENCE_VIA_FULLY_QUALIFIED_NAME);
							$fix = false;
						}

						if ($fix) {
							if (count($useStatements) === 0) {
								$namespacePointer = TokenHelper::findNext($phpcsFile, T_NAMESPACE, $openTagPointer);
								$useStatementPlacePointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $namespacePointer + 1);
							} else {
								$lastUseStatement = array_values($useStatements)[count($useStatements) - 1];
								$useStatementPlacePointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $lastUseStatement->getPointer() + 1);
							}

							$phpcsFile->fixer->beginChangeset();

							for ($i = $referencedName->getStartPointer(); $i <= $referencedName->getEndPointer(); $i++) {
								$phpcsFile->fixer->replaceToken($i, '');
							}

							$alreadyUsed = false;
							foreach ($useStatements as $useStatement) {
								if ($useStatement->getFullyQualifiedTypeName() === $canonicalName) {
									$nameToReference = $useStatement->getNameAsReferencedInFile();
									$alreadyUsed = true;
									break;
								}
							}

							$phpcsFile->fixer->addContent($referencedName->getStartPointer(), $nameToReference);

							if (!$alreadyUsed) {
								$phpcsFile->fixer->addNewline($useStatementPlacePointer);
								$phpcsFile->fixer->addContent($useStatementPlacePointer, sprintf('use %s;', $canonicalName));
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
					), $nameStartPointer, self::CODE_PARTIAL_USE);
				}
			}
		}
	}

	private function isClassRequiredToBeUsed(string $name): bool
	{
		if (count($this->namespacesRequiredToUse) === 0) {
			return true;
		}

		foreach ($this->getNamespacesRequiredToUse() as $namespace) {
			if (NamespaceHelper::isTypeInNamespace($name, $namespace)) {
				return true;
			}
		}

		return false;
	}

}
