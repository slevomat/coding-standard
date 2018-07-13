<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use Exception;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\CatchHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use Throwable;
use const T_BITWISE_OR;
use const T_CATCH;
use const T_EXTENDS;
use const T_INSTANCEOF;
use const T_NEW;
use const T_OPEN_PARENTHESIS;
use const T_OPEN_TAG;
use function array_merge;
use function in_array;
use function sprintf;

class ReferenceThrowableOnlySniff implements Sniff
{

	public const CODE_REFERENCED_GENERAL_EXCEPTION = 'ReferencedGeneralException';

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
	public function process(File $phpcsFile, $openTagPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$message = sprintf('Referencing general \%s; use \%s instead.', Exception::class, Throwable::class);
		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);
		$referencedNames = ReferencedNameHelper::getAllReferencedNames($phpcsFile, $openTagPointer);
		foreach ($referencedNames as $referencedName) {
			$resolvedName = NamespaceHelper::resolveClassName(
				$phpcsFile,
				$referencedName->getNameAsReferencedInFile(),
				$useStatements,
				$referencedName->getStartPointer()
			);
			if ($resolvedName !== '\\Exception') {
				continue;
			}
			$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $referencedName->getStartPointer() - 1);
			if (in_array($tokens[$previousPointer]['code'], [T_EXTENDS, T_NEW, T_INSTANCEOF], true)) {
				continue; // Allow \Exception in extends and instantiating it
			}
			if ($tokens[$previousPointer]['code'] === T_BITWISE_OR) {
				$previousPointer = TokenHelper::findPreviousExcluding($phpcsFile, array_merge(TokenHelper::$ineffectiveTokenCodes, TokenHelper::$nameTokenCodes, [T_BITWISE_OR]), $previousPointer - 1);
			}
			if ($tokens[$previousPointer]['code'] === T_OPEN_PARENTHESIS) {
				/** @var int $catchPointer */
				$catchPointer = TokenHelper::findPreviousEffective($phpcsFile, $previousPointer - 1);
				if ($tokens[$catchPointer]['code'] === T_CATCH) {
					if ($this->searchForThrowableInNextCatches($phpcsFile, $useStatements, $catchPointer)) {
						continue;
					}
				}
			}

			$fix = $phpcsFile->addFixableError(
				$message,
				$referencedName->getStartPointer(),
				self::CODE_REFERENCED_GENERAL_EXCEPTION
			);
			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			for ($i = $referencedName->getStartPointer(); $i <= $referencedName->getEndPointer(); $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			$phpcsFile->fixer->addContent($referencedName->getStartPointer(), '\Throwable');
			$phpcsFile->fixer->endChangeset();
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param \SlevomatCodingStandard\Helpers\UseStatement[] $useStatements
	 * @param int $catchPointer
	 * @return bool
	 */
	private function searchForThrowableInNextCatches(File $phpcsFile, array $useStatements, int $catchPointer): bool
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
