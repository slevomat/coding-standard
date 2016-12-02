<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use SlevomatCodingStandard\Helpers\CatchHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class ReferenceThrowableOnlySniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_REFERENCED_GENERAL_EXCEPTION = 'ReferencedGeneralException';

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
		$tokens = $phpcsFile->getTokens();
		$message = sprintf('Referencing general \%s; use \%s instead', \Exception::class, \Throwable::class);
		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);
		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
		foreach ($referencedNames as $referencedName) {
			$resolvedName = NamespaceHelper::resolveName(
				$phpcsFile,
				$referencedName->getNameAsReferencedInFile(),
				$useStatements,
				$referencedName->getPointer()
			);
			if ($resolvedName !== '\\Exception') {
				continue;
			}
			$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $referencedName->getPointer() - 1);
			if (in_array($tokens[$previousPointer]['code'], [T_EXTENDS, T_NEW, T_INSTANCEOF], true)) {
				continue; // allow \Exception in extends and instantiating it
			}
			if (
				PHP_VERSION_ID >= 70100
				&& $tokens[$previousPointer]['code'] === T_BITWISE_OR
			) {
				$previousPointer = TokenHelper::findPreviousExcluding($phpcsFile, array_merge(
					TokenHelper::$ineffectiveTokenCodes,
					TokenHelper::$nameTokenCodes,
					[T_BITWISE_OR]
				), $previousPointer - 1);
			}
			if ($tokens[$previousPointer]['code'] === T_OPEN_PARENTHESIS) {
				$catchPointer = TokenHelper::findPreviousEffective($phpcsFile, $previousPointer - 1);
				if ($tokens[$catchPointer]['code'] === T_CATCH) {
					if ($this->searchForThrowableInNextCatches($phpcsFile, $useStatements, $catchPointer)) {
						continue;
					}
				}
			}
			$phpcsFile->addError(
				$message,
				$referencedName->getPointer(),
				self::CODE_REFERENCED_GENERAL_EXCEPTION
			);
		}
	}

	private function searchForThrowableInNextCatches(\PHP_CodeSniffer_File $phpcsFile, array $useStatements, int $catchPointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$nextCatchPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$catchPointer]['scope_closer'] + 1);

		while ($nextCatchPointer !== null) {
			$nextCatchToken = $tokens[$nextCatchPointer];
			if ($nextCatchToken['code'] !== T_CATCH) {
				break;
			}

			$catchedTypes = CatchHelper::findCatchedTypesInCatch($phpcsFile, $useStatements, $nextCatchToken);
			if (in_array('\\Throwable', $catchedTypes, true)) {
				return true;
			}

			$nextCatchPointer = TokenHelper::findNextEffective($phpcsFile, $nextCatchToken['scope_closer'] + 1);
		}

		return false;
	}

}
