<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedName;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

abstract class AbstractFullyQualifiedGlobalReference
{

	public const CODE_NON_FULLY_QUALIFIED = 'NonFullyQualified';

	public const CODE_FULLY_QUALIFIED_NOT_ALLOWED = 'FullyQualifiedNotAllowed';

	public const CODE_IMPORTED_NOT_ALLOWED = 'ImportedNotAllowed';

	/** @var string[] */
	public $exclude = [];

	/** @var string[]|null */
	private $normalizedExclude;

	/** @var bool */
	public $allowFullyQualified = true;

	/** @var bool */
	public $allowImported = true;

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
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $openTagPointer): void
	{
		assert($this->allowFullyQualified || $this->allowImported, 'At least one check must be enabled.');

		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);
		$exclude = array_flip($this->getNormalizedExclude());

		foreach ($referencedNames as $referencedName) {
			$name = $referencedName->getNameAsReferencedInFile();
			$canonicalName = $this->isCaseSensitive() ? $name : strtolower($name);
			$referenceNamePointer = $referencedName->getStartPointer();

			if (!$this->isValidType($referencedName)) {
				continue;
			}

			if (NamespaceHelper::isFullyQualifiedName($name)) {
				if (!$this->allowFullyQualified && !NamespaceHelper::hasNamespace($name)) {
					$phpcsFile->addError(sprintf($this->getFullyQualifiedNotAllowedMessage(), $name), $referenceNamePointer, self::CODE_FULLY_QUALIFIED_NOT_ALLOWED);
				}
				continue;
			}

			if (NamespaceHelper::hasNamespace($name)) {
				continue;
			}

			if (array_key_exists($canonicalName, $exclude)) {
				continue;
			}

			if (array_key_exists($canonicalName, $useStatements)) {
				if (!$this->allowImported && !NamespaceHelper::hasNamespace($useStatements[$canonicalName]->getFullyQualifiedTypeName())) {
					$phpcsFile->addError(sprintf($this->getImportedNotAllowedMessage(), $name), $referenceNamePointer, self::CODE_IMPORTED_NOT_ALLOWED);
				}

				continue;
			}

			$fix = $phpcsFile->addFixableError(sprintf($this->getNotFullyQualifiedMessage(), $name), $referenceNamePointer, self::CODE_NON_FULLY_QUALIFIED);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContentBefore($referenceNamePointer, NamespaceHelper::NAMESPACE_SEPARATOR);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * @return string[]
	 */
	private function getNormalizedExclude(): array
	{
		if ($this->normalizedExclude === null) {
			$exclude = SniffSettingsHelper::normalizeArray($this->exclude);

			if (!$this->isCaseSensitive()) {
				$exclude = array_map(function (string $name): string {
					return strtolower($name);
				}, $exclude);
			}

			$this->normalizedExclude = $exclude;
		}
		return $this->normalizedExclude;
	}

	abstract protected function getNotFullyQualifiedMessage(): string;

	abstract protected function getFullyQualifiedNotAllowedMessage(): string;

	abstract protected function getImportedNotAllowedMessage(): string;

	abstract protected function isCaseSensitive(): bool;

	abstract protected function isValidType(ReferencedName $name): bool;

}
