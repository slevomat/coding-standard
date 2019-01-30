<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use const T_EMPTY;

class DisallowEmptySniff implements Sniff
{

	public const CODE_DISALLOWED_EMPTY = 'DisallowedEmpty';

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_EMPTY,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $emptyPointer
	 */
	public function process(File $phpcsFile, $emptyPointer): void
	{
		$phpcsFile->addError('Use of empty() is disallowed.', $emptyPointer, self::CODE_DISALLOWED_EMPTY);
	}

}
