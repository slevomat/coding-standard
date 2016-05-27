<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class FullyQualifiedExceptionsSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_NON_FULLY_QUALIFIED_EXCEPTION = 'NonFullyQualifiedException';

	/** @var string[] */
	public $specialExceptionNames = [];

	/** @var string[] */
	private $normalizedSpecialExceptionNames;

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
	 * @return string[]
	 */
	private function getSpecialExceptionNames()
	{
		if ($this->normalizedSpecialExceptionNames === null) {
			$this->normalizedSpecialExceptionNames = SniffSettingsHelper::normalizeArray($this->specialExceptionNames);
		}

		return $this->normalizedSpecialExceptionNames;
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $openTagPointer
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $openTagPointer)
	{
		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);
		foreach ($referencedNames as $referencedName) {
			$pointer = $referencedName->getPointer();
			$name = $referencedName->getNameAsReferencedInFile();
			$normalizedName = UseStatement::normalizedNameAsReferencedInFile($name);
			if (isset($useStatements[$normalizedName])) {
				$useStatement = $useStatements[$normalizedName];
				if (
					!StringHelper::endsWith($useStatement->getFullyQualifiedTypeName(), 'Exception')
					&& $useStatement->getFullyQualifiedTypeName() !== 'Throwable'
					&& (!StringHelper::endsWith($useStatement->getFullyQualifiedTypeName(), 'Error') || NamespaceHelper::hasNamespace($useStatement->getFullyQualifiedTypeName()))
					&& !in_array($useStatement->getFullyQualifiedTypeName(), $this->getSpecialExceptionNames(), true)
				) {
					continue;
				}
			} else {
				$fileNamespace = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $pointer);
				$canonicalName = $name;
				if (!NamespaceHelper::isFullyQualifiedName($name) && $fileNamespace !== null) {
					$canonicalName = sprintf('%s%s%s', $fileNamespace, NamespaceHelper::NAMESPACE_SEPARATOR, $name);
				}
				if (
					!StringHelper::endsWith($name, 'Exception')
					&& $name !== 'Throwable'
					&& (!StringHelper::endsWith($canonicalName, 'Error') || NamespaceHelper::hasNamespace($canonicalName))
					&& !in_array($canonicalName, $this->getSpecialExceptionNames(), true)) {
					continue;
				}
			}

			if (!NamespaceHelper::isFullyQualifiedName($name)) {
				$phpcsFile->addError(sprintf(
					'Exception %s should be referenced via a fully qualified name',
					$name
				), $pointer, self::CODE_NON_FULLY_QUALIFIED_EXCEPTION);
			}
		}
	}

}
