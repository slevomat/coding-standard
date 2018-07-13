<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use const T_NAMESPACE;

class RequireOneNamespaceInFileSniff implements Sniff
{

	public const CODE_MORE_NAMESPACES_IN_FILE = 'MoreNamespacesInFile';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_NAMESPACE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $namespacePointer
	 */
	public function process(File $phpcsFile, $namespacePointer): void
	{
		if (TokenHelper::findPrevious($phpcsFile, T_NAMESPACE, $namespacePointer - 1) === null) {
			return;
		}

		$phpcsFile->addError('Only one namespace in a file is allowed.', $namespacePointer, self::CODE_MORE_NAMESPACES_IN_FILE);
	}

}
