<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class AlphabeticallySortedUsesSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_INCORRECT_ORDER = 'IncorrectlyOrderedUses';

	/** @var string */
	private $lastUseTypeName;

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
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $openTagPointer)
	{
		$this->lastUseTypeName = null;
		$useStatements = UseStatementHelper::getUseStatements(
			$phpcsFile,
			$openTagPointer
		);
		foreach ($useStatements as $useStatement) {
			$typeName = $useStatement->getFullyQualifiedTypeName();
			if ($this->lastUseTypeName === null) {
				$this->lastUseTypeName = $typeName;
			} else {
				$order = $this->compareStrings($typeName, $this->lastUseTypeName);
				if ($order < 0) {
					$fix = $phpcsFile->addFixableError(
						sprintf('Use statements are incorrectly ordered. The first wrong one is %s', $typeName),
						$useStatement->getPointer(),
						self::CODE_INCORRECT_ORDER
					);
					if ($fix) {
						$this->fixAlphabeticalOrder($phpcsFile, $useStatements);
					}

					return;
				} else {
					$this->lastUseTypeName = $typeName;
				}
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param \SlevomatCodingStandard\Helpers\UseStatement[] $useStatements
	 */
	private function fixAlphabeticalOrder(
		\PHP_CodeSniffer_File $phpcsFile,
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
			return $this->compareStrings($a->getFullyQualifiedTypeName(), $b->getFullyQualifiedTypeName());
		});

		$phpcsFile->fixer->addContent($firstUseStatement->getPointer(), implode(PHP_EOL, array_map(function (UseStatement $useStatement): string {
			$unqualifiedName = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($useStatement->getFullyQualifiedTypeName());
			if ($unqualifiedName === $useStatement->getNameAsReferencedInFile()) {
				return sprintf('use %s;', $useStatement->getFullyQualifiedTypeName());
			}

			return sprintf('use %s as %s;', $useStatement->getFullyQualifiedTypeName(), $useStatement->getNameAsReferencedInFile());
		}, $useStatements)));
		$phpcsFile->fixer->endChangeset();
	}

	private function compareStrings(string $a, string $b): int
	{
		$i = 0;
		for (; $i < min(strlen($a), strlen($b)); $i++) {
			if ($this->isSpecialCharacter($a[$i]) && !$this->isSpecialCharacter($b[$i])) {
				return -1;
			} elseif (!$this->isSpecialCharacter($a[$i]) && $this->isSpecialCharacter($b[$i])) {
				return 1;
			}

			if (is_numeric($a[$i]) && is_numeric($b[$i])) {
				break;
			}

			$cmp = strcasecmp($a[$i], $b[$i]);
			if (
				$cmp !== 0
				|| ($a[$i] !== $b[$i] && strtolower($a[$i]) === strtolower($b[$i]))
			) {
				return $cmp;
			}
		}

		return strnatcasecmp(substr($a, $i), substr($b, $i));
	}

	private function isSpecialCharacter(string $character): bool
	{
		return in_array($character, ['\\', '_'], true);
	}

}
