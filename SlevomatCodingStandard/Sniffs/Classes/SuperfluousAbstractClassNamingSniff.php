<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class SuperfluousAbstractClassNamingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_SUPERFLUOUS_PREFIX = 'SuperfluousPrefix';
	public const CODE_SUPERFLUOUS_SUFFIX = 'SuperfluousSuffix';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_CLASS,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $classPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $classPointer): void
	{
		$className = ClassHelper::getName($phpcsFile, $classPointer);

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $classPointer - 1);
		if ($phpcsFile->getTokens()[$previousPointer]['code'] !== T_ABSTRACT) {
			return;
		}

		$this->checkPrefix($phpcsFile, $classPointer, $className);
		$this->checkSuffix($phpcsFile, $classPointer, $className);
	}

	private function checkPrefix(\PHP_CodeSniffer\Files\File $phpcsFile, int $classPointer, string $className): void
	{
		$prefix = substr($className, 0, 8);

		if (strtolower($prefix) !== 'abstract') {
			return;
		}

		$phpcsFile->addError(sprintf('Superfluous prefix "%s".', $prefix), $classPointer, self::CODE_SUPERFLUOUS_PREFIX);
	}

	private function checkSuffix(\PHP_CodeSniffer\Files\File $phpcsFile, int $classPointer, string $className): void
	{
		$suffix = substr($className, -8);

		if (strtolower($suffix) !== 'abstract') {
			return;
		}

		$phpcsFile->addError(sprintf('Superfluous suffix "%s".', $suffix), $classPointer, self::CODE_SUPERFLUOUS_SUFFIX);
	}

}
