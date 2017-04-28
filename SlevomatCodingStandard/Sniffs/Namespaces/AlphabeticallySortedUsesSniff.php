<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class AlphabeticallySortedUsesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_INCORRECT_ORDER = 'IncorrectlyOrderedUses';

	/** @var bool */
	public $caseSensitive = false;

	/** @var \SlevomatCodingStandard\Helpers\UseStatement|null */
	private $lastUse;

	/**
	 * @return int[]
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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $openTagPointer)
	{
		$this->lastUse = null;
		$useStatements = UseStatementHelper::getUseStatements(
			$phpcsFile,
			$openTagPointer
		);
		foreach ($useStatements as $useStatement) {
			if ($this->lastUse === null) {
				$this->lastUse = $useStatement;
			} else {
				$order = $this->compareUseStatements($useStatement, $this->lastUse);
				if ($order < 0) {
					$fix = $phpcsFile->addFixableError(
						sprintf('Use statements are incorrectly ordered. The first wrong one is %s.', $useStatement->getFullyQualifiedTypeName()),
						$useStatement->getPointer(),
						self::CODE_INCORRECT_ORDER
					);
					if ($fix) {
						$this->fixAlphabeticalOrder($phpcsFile, $useStatements);
					}

					return;
				} else {
					$this->lastUse = $useStatement;
				}
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param \SlevomatCodingStandard\Helpers\UseStatement[] $useStatements
	 */
	private function fixAlphabeticalOrder(
		\PHP_CodeSniffer\Files\File $phpcsFile,
		array $useStatements
	)
	{
		$firstUseStatement = reset($useStatements);
		$lastUseStatement = end($useStatements);
		$lastSemicolonPointer = $phpcsFile->findNext(T_SEMICOLON, $lastUseStatement->getPointer());
		$phpcsFile->fixer->beginChangeset();
		for ($i = $firstUseStatement->getPointer(); $i <= $lastSemicolonPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		uasort($useStatements, function (UseStatement $a, UseStatement $b) {
			return $this->compareUseStatements($a, $b);
		});

		$phpcsFile->fixer->addContent($firstUseStatement->getPointer(), implode($phpcsFile->eolChar, array_map(function (UseStatement $useStatement): string {
			$unqualifiedName = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($useStatement->getFullyQualifiedTypeName());
			if ($unqualifiedName === $useStatement->getNameAsReferencedInFile()) {
				return sprintf('use %s;', $useStatement->getFullyQualifiedTypeName());
			}

			return sprintf('use %s as %s;', $useStatement->getFullyQualifiedTypeName(), $useStatement->getNameAsReferencedInFile());
		}, $useStatements)));
		$phpcsFile->fixer->endChangeset();
	}

	private function compareUseStatements(UseStatement $a, UseStatement $b): int
	{
		if (!$a->hasSameType($b)) {
			return $a->compareByType($b);
		}

		$aNameParts = explode(NamespaceHelper::NAMESPACE_SEPARATOR, $a->getFullyQualifiedTypeName());
		$bNameParts = explode(NamespaceHelper::NAMESPACE_SEPARATOR, $b->getFullyQualifiedTypeName());

		for ($i = 0; $i < min(count($aNameParts), count($bNameParts)); $i++) {
			$comparison = $this->compare($aNameParts[$i], $bNameParts[$i]);
			if ($comparison !== 0) {
				return $comparison;
			}
		}

		return count($aNameParts) <=> count($bNameParts);
	}

	private function compare(string $a, string $b): int
	{
		if ($this->caseSensitive) {
			return strcmp($a, $b);
		}

		return strcasecmp($a, $b);
	}

}
