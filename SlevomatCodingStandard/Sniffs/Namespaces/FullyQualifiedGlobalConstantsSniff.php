<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class FullyQualifiedGlobalConstantsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_NON_FULLY_QUALIFIED = 'NonFullyQualified';

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
		$tokens = $phpcsFile->getTokens();

		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);

		foreach ($referencedNames as $referencedName) {
			$name = $referencedName->getNameAsReferencedInFile();
			$referenceNamePointer = $referencedName->getStartPointer();

			if (!$referencedName->isConstant()) {
				continue;
			}

			if (NamespaceHelper::isFullyQualifiedName($name)) {
				continue;
			}

			if (NamespaceHelper::hasNamespace($name)) {
				continue;
			}

			if (array_key_exists($name, $useStatements)) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(sprintf('Constant %s should be referenced via a fully qualified name.', $tokens[$referenceNamePointer]['content']), $referenceNamePointer, self::CODE_NON_FULLY_QUALIFIED);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContentBefore($referenceNamePointer, NamespaceHelper::NAMESPACE_SEPARATOR);
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
