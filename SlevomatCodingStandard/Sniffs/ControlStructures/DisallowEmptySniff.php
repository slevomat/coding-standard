<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class DisallowEmptySniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_DISALLOWED_EMPTY = 'DisallowedEmpty';

	/**
	 * @return mixed[]
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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $emptyPointer): void
	{
		$phpcsFile->addError('Use of empty() is disallowed.', $emptyPointer, self::CODE_DISALLOWED_EMPTY);
	}

}
