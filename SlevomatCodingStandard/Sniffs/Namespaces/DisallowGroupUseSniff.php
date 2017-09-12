<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class DisallowGroupUseSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_DISALLOWED_GROUP_USE = 'DisallowedGroupUse';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_USE_GROUP,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $usePointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $usePointer): void
	{
		$phpcsFile->addError('Group use declaration is disallowed, use single use for every import.', $usePointer, self::CODE_DISALLOWED_GROUP_USE);
	}

}
