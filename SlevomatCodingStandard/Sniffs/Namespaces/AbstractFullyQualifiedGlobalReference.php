<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedName;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function array_flip;
use function array_key_exists;
use function array_map;
use function sprintf;
use function strtolower;
use const T_OPEN_TAG;
use const T_OPEN_USE_GROUP;

/**
 * @internal
 */
abstract class AbstractFullyQualifiedGlobalReference implements Sniff
{

	public const CODE_NON_FULLY_QUALIFIED = 'NonFullyQualified';

	/** @var list<string> */
	public array $exclude = [];

	/** @var list<string> */
	public array $include = [];

	/** @var list<string>|null */
	private ?array $normalizedExclude = null;

	/** @var list<string>|null */
	private ?array $normalizedInclude = null;

	abstract protected function getNotFullyQualifiedMessage(): string;

	abstract protected function isCaseSensitive(): bool;

	abstract protected function isValidType(ReferencedName $name): bool;

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

		if (TokenHelper::findNext($phpcsFile, T_OPEN_USE_GROUP, $openTagPointer) !== null) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$namespacePointers = NamespaceHelper::getAllNamespacesPointers($phpcsFile);
		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
		$include = array_flip($this->getNormalizedInclude());
		$exclude = array_flip($this->getNormalizedExclude());

		foreach ($referencedNames as $referencedName) {
			$name = $referencedName->getNameAsReferencedInFile();
			$namePointer = $referencedName->getStartPointer();

			if (!$this->isValidType($referencedName)) {
				continue;
			}

			if (NamespaceHelper::isFullyQualifiedName($name)) {
				continue;
			}

			if (NamespaceHelper::hasNamespace($name)) {
				continue;
			}

			if ($namespacePointers === []) {
				continue;
			}

			$canonicalName = $this->isCaseSensitive() ? $name : strtolower($name);

			$useStatements = UseStatementHelper::getUseStatementsForPointer($phpcsFile, $namePointer);

			if (array_key_exists(UseStatement::getUniqueId($referencedName->getType(), $canonicalName), $useStatements)) {
				$fullyQualifiedName = NamespaceHelper::resolveName($phpcsFile, $name, $referencedName->getType(), $namePointer);
				if (NamespaceHelper::hasNamespace($fullyQualifiedName)) {
					continue;
				}
			}

			if ($include !== [] && !array_key_exists($canonicalName, $include)) {
				continue;
			}

			if (array_key_exists($canonicalName, $exclude)) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf($this->getNotFullyQualifiedMessage(), $tokens[$namePointer]['content']),
				$namePointer,
				self::CODE_NON_FULLY_QUALIFIED,
			);
			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();
			$phpcsFile->fixer->addContentBefore($namePointer, NamespaceHelper::NAMESPACE_SEPARATOR);
			$phpcsFile->fixer->endChangeset();
		}
	}

	/**
	 * @return list<string>
	 */
	protected function getNormalizedInclude(): array
	{
		if ($this->normalizedInclude === null) {
			$this->normalizedInclude = $this->normalizeNames($this->include);
		}
		return $this->normalizedInclude;
	}

	/**
	 * @return list<string>
	 */
	private function getNormalizedExclude(): array
	{
		if ($this->normalizedExclude === null) {
			$this->normalizedExclude = $this->normalizeNames($this->exclude);
		}
		return $this->normalizedExclude;
	}

	/**
	 * @param list<string> $names
	 * @return list<string>
	 */
	private function normalizeNames(array $names): array
	{
		$names = SniffSettingsHelper::normalizeArray($names);

		if (!$this->isCaseSensitive()) {
			$names = array_map(static fn (string $name): string => strtolower($name), $names);
		}

		return $names;
	}

}
