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
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_NAMESPACE,
		];
	}

	public function process(File $phpcsFile, int $namespacePointer): void
	{
		$previousNamespacePointer = TokenHelper::findPrevious($phpcsFile, T_NAMESPACE, $namespacePointer - 1);
		if ($previousNamespacePointer === null) {
			return;
		}

		$phpcsFile->addError('Only one namespace in a file is allowed.', $namespacePointer, self::CODE_MORE_NAMESPACES_IN_FILE);
	}

}
