<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

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
	 * @return int[]
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
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $openTagPointer)
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
				if (!StringHelper::endsWith($name, 'Exception') && !in_array($canonicalName, $this->getSpecialExceptionNames(), true)) {
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
