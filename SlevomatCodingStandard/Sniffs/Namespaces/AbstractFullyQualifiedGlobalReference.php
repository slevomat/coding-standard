<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedName;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function array_flip;
use function array_key_exists;
use function array_map;
use function sprintf;
use function strtolower;
use const T_OPEN_TAG;

/**
 * @internal
 */
abstract class AbstractFullyQualifiedGlobalReference implements Sniff
{

	public const CODE_NON_FULLY_QUALIFIED = 'NonFullyQualified';

	/** @var string[] */
	public $exclude = [];

	/** @var string[]|null */
	private $normalizedExclude;

	abstract protected function getNotFullyQualifiedMessage(): string;

	abstract protected function isCaseSensitive(): bool;

	abstract protected function isValidType(ReferencedName $name): bool;

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
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
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

			if (NamespaceHelper::findCurrentNamespaceName($phpcsFile, $namePointer) === null) {
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

			if (array_key_exists($canonicalName, $exclude)) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(sprintf($this->getNotFullyQualifiedMessage(), $tokens[$namePointer]['content']), $namePointer, self::CODE_NON_FULLY_QUALIFIED);
			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();
			$phpcsFile->fixer->addContentBefore($namePointer, NamespaceHelper::NAMESPACE_SEPARATOR);
			$phpcsFile->fixer->endChangeset();
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
				$exclude = array_map(static function (string $name): string {
					return strtolower($name);
				}, $exclude);
			}

			$this->normalizedExclude = $exclude;
		}
		return $this->normalizedExclude;
	}

}
