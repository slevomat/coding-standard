<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Helpers\ClassHelper;

class SuperfluousInterfaceNamingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_SUPERFLUOUS_PREFIX = 'SuperfluousPrefix';
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

		$this->checkPrefix($phpcsFile, $interfacePointer, $interfaceName);
		$this->checkSuffix($phpcsFile, $interfacePointer, $interfaceName);
	}

	private function checkPrefix(\PHP_CodeSniffer\Files\File $phpcsFile, int $interfacePointer, string $interfaceName): void
	{
		$prefix = substr($interfaceName, 0, 9);

		if (strtolower($prefix) !== 'interface') {
			return;
		}

		$phpcsFile->addError(sprintf('Superfluous prefix "%s".', $prefix), $interfacePointer, self::CODE_SUPERFLUOUS_PREFIX);
	}

	private function checkSuffix(\PHP_CodeSniffer\Files\File $phpcsFile, int $interfacePointer, string $interfaceName): void
	{
		$suffix = substr($interfaceName, -9);

		if (strtolower($suffix) !== 'interface') {
			return;
		}

		$phpcsFile->addError(sprintf('Superfluous suffix "%s".', $suffix), $interfacePointer, self::CODE_SUPERFLUOUS_SUFFIX);
	}

}
