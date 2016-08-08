<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class UnusedUsesSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_UNUSED_USE = 'UnusedUse';
	const CODE_MISMATCHING_CASE = 'MismatchingCaseSensitivity';

	/** @var boolean */
	public $searchAnnotations = false;

	/**
	 * @return integer[]
	 */
	public function register()
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $openTagPointer
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $openTagPointer)
	{
		$unusedNames = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);
		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer, $this->searchAnnotations);

		foreach ($referencedNames as $referencedName) {
			$name = $referencedName->getNameAsReferencedInFile();
			$pointer = $referencedName->getPointer();
			$nameParts = NamespaceHelper::getNameParts($name);
			$nameAsReferencedInFile = $nameParts[0];
			$normalizedNameAsReferencedInFile = UseStatement::normalizedNameAsReferencedInFile($nameAsReferencedInFile);
			if (
				!NamespaceHelper::isFullyQualifiedName($name)
				&& isset($unusedNames[$normalizedNameAsReferencedInFile])
			) {
				if (!$referencedName->hasSameUseStatementType($unusedNames[$normalizedNameAsReferencedInFile])) {
					continue;
				}
				if ($unusedNames[$normalizedNameAsReferencedInFile]->getNameAsReferencedInFile() !== $nameAsReferencedInFile) {
					$phpcsFile->addError(sprintf(
						'Case of reference name %s and use statement %s do not match',
						$nameAsReferencedInFile,
						$unusedNames[$normalizedNameAsReferencedInFile]->getNameAsReferencedInFile()
					), $pointer, self::CODE_MISMATCHING_CASE);
				}
				unset($unusedNames[$normalizedNameAsReferencedInFile]);
			}
		}

		foreach ($unusedNames as $value) {
			$fullName = $value->getFullyQualifiedTypeName();
			if ($value->getNameAsReferencedInFile() !== $fullName) {
				if ($value->getNameAsReferencedInFile() !== NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($fullName)) {
					$fullName .= sprintf(' (as %s)', $value->getNameAsReferencedInFile());
				}
			}
			$fix = $phpcsFile->addFixableError(sprintf(
				'Type %s is not used in this file',
				$fullName
			), $value->getPointer(), self::CODE_UNUSED_USE);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$endPointer = $phpcsFile->findNext(T_SEMICOLON, $value->getPointer()) + 1;
				for ($i = $value->getPointer(); $i <= $endPointer; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
