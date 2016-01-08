<?php

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class ReferenceThrowableOnlySniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_REFERENCED_GENERAL_EXCEPTION = 'ReferencedGeneralException';

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
		$tokens = $phpcsFile->getTokens();
		$message = sprintf('Referencing general \%s; use \%s instead', \Exception::class, \Throwable::class);
		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);
		foreach ($useStatements as $useStatement) {
			if ($useStatement->getFullyQualifiedTypeName() === \Exception::class) {
				$phpcsFile->addError(
					$message,
					$useStatement->getPointer(),
					self::CODE_REFERENCED_GENERAL_EXCEPTION
				);
			}
		}

		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
		if (count($referencedNames) > 0) {
			$currentNamespace = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $referencedNames[0]->getPointer());
		} else {
			return;
		}
		foreach ($referencedNames as $referencedName) {
			if ($referencedName->getNameAsReferencedInFile() === 'Exception' && $currentNamespace !== null) {
				continue;
			}
			if (!in_array($referencedName->getNameAsReferencedInFile(), ['\Exception', 'Exception'], true)) {
				continue;
			}
			$previousPointer = TokenHelper::findPreviousNonWhitespace($phpcsFile, $referencedName->getPointer() - 1);
			if (in_array($tokens[$previousPointer]['code'], [T_EXTENDS, T_NEW, T_INSTANCEOF], true)) {
				continue; // allow \Exception in extends and instantiating it
			}
			$phpcsFile->addError(
				$message,
				$referencedName->getPointer(),
				self::CODE_REFERENCED_GENERAL_EXCEPTION
			);
		}
	}

}
