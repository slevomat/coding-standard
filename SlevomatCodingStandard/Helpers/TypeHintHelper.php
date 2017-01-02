<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHintHelper
{

	/** @var string[] */
	public static $simpleTypeHints = [
		'int',
		'integer',
		'float',
		'string',
		'bool',
		'boolean',
		'callable',
		'self',
		'array',
		'iterable',
		'void',
	];

	public static function getFullyQualifiedTypeHint(\PHP_CodeSniffer_File $phpcsFile, int $pointer, string $typeHint): string
	{
		if (in_array($typeHint, self::$simpleTypeHints, true) || NamespaceHelper::isFullyQualifiedName($typeHint)) {
			return $typeHint;
		}

		$canonicalQualifiedTypeHint = $typeHint;

		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $phpcsFile->findPrevious(T_OPEN_TAG, $pointer));
		$normalizedTypeHint = UseStatement::normalizedNameAsReferencedInFile($typeHint);
		if (isset($useStatements[$normalizedTypeHint])) {
			$useStatement = $useStatements[$normalizedTypeHint];
			$canonicalQualifiedTypeHint = $useStatement->getFullyQualifiedTypeName();
		} else {
			$fileNamespace = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $pointer);
			if ($fileNamespace !== null) {
				$canonicalQualifiedTypeHint = sprintf('%s%s%s', $fileNamespace, NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}
		}

		return sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $canonicalQualifiedTypeHint);
	}

}
