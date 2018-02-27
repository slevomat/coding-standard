<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Helpers\ClassHelper;

class SuperfluousInterfaceNamingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_SUPERFLUOUS_SUFFIX = 'SuperfluousSuffix';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_INTERFACE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $interfacePointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $interfacePointer): void
	{
		$interfaceName = ClassHelper::getName($phpcsFile, $interfacePointer);

		$suffix = substr($interfaceName, -9);
		if (strtolower($suffix) !== 'interface') {
			return;
		}

		$phpcsFile->addError(sprintf('Superfluous suffix "%s".', $suffix), $interfacePointer, self::CODE_SUPERFLUOUS_SUFFIX);
	}

}
