<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class DisallowGroupUseSniff implements \PHP_CodeSniffer_Sniff
{

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_USE_GROUP,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $usePointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $usePointer)
	{
		$phpcsFile->addError('Group use declaration is disallowed, use single use for every import.', $usePointer);
	}

}
