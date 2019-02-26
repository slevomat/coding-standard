<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;
use function count;
use function sprintf;
use const T_AS;
use const T_OPEN_TAG;
use const T_SEMICOLON;
use const T_STRING;

class UselessAliasSniff implements Sniff
{

	public const CODE_USELESS_ALIAS = 'UselessAlias';

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
	{
		$fileUseStatements = UseStatementHelper::getFileUseStatements($phpcsFile);

		if (count($fileUseStatements) === 0) {
			return;
		}

		foreach ($fileUseStatements as $useStatements) {
			foreach ($useStatements as $useStatement) {
				if ($useStatement->getAlias() === null) {
					continue;
				}

				$unqualifiedName = NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($useStatement->getFullyQualifiedTypeName());
				if ($unqualifiedName !== $useStatement->getAlias()) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(
					sprintf('Useless alias "%s" for use of "%s".', $useStatement->getAlias(), $useStatement->getFullyQualifiedTypeName()),
					$useStatement->getPointer(),
					self::CODE_USELESS_ALIAS
				);

				if (!$fix) {
					continue;
				}

				$asPointer = TokenHelper::findNext($phpcsFile, T_AS, $useStatement->getPointer() + 1);
				$nameEndPointer = TokenHelper::findPrevious($phpcsFile, T_STRING, $asPointer - 1);
				$useSemicolonPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $asPointer + 1);

				$phpcsFile->fixer->beginChangeset();
				for ($i = $nameEndPointer + 1; $i < $useSemicolonPointer; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
