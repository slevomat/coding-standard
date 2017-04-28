<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class UseFromSameNamespaceSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_USE_FROM_SAME_NAMESPACE = 'UseFromSameNamespace';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_USE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $usePointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $usePointer)
	{
		if (
			UseStatementHelper::isAnonymousFunctionUse($phpcsFile, $usePointer)
			|| UseStatementHelper::isTraitUse($phpcsFile, $usePointer)
		) {
			return;
		}

		$namespaceName = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $usePointer);
		if ($namespaceName === null) {
			$namespaceName = '';
		}

		$usedTypeName = UseStatementHelper::getFullyQualifiedTypeNameFromUse($phpcsFile, $usePointer);
		if (!StringHelper::startsWith($usedTypeName, $namespaceName)) {
			return;
		}

		$asPointer = $this->findAsPointer($phpcsFile, $usePointer);
		if ($asPointer !== null) {
			return;
		}

		$usedTypeNameRest = substr($usedTypeName, strlen($namespaceName));
		if (!NamespaceHelper::isFullyQualifiedName($usedTypeNameRest) && $namespaceName !== '') {
			return;
		}

		if (!NamespaceHelper::hasNamespace($usedTypeNameRest)) {
			$fix = $phpcsFile->addFixableError(sprintf(
				'Use %s is from the same namespace – that is prohibited.',
				$usedTypeName
			), $usePointer, self::CODE_USE_FROM_SAME_NAMESPACE);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$endPointer = $phpcsFile->findNext(T_SEMICOLON, $usePointer) + 1;
				for ($i = $usePointer; $i <= $endPointer; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $startPointer
	 * @return int|null
	 */
	private function findAsPointer(\PHP_CodeSniffer\Files\File $phpcsFile, int $startPointer)
	{
		$asPointer = $phpcsFile->findNext(T_AS, $startPointer, null, false, null, true);
		if ($asPointer === false) {
			return null;
		}
		return $asPointer;
	}

}
