<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Variables;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use function in_array;
use const T_VARIABLE;

class DisallowSuperGlobalVariableSniff implements Sniff
{

	public const CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE = 'DisallowedSuperGlobalVariable';

	private const SUPER_GLOBALS = [
		'$GLOBALS',
		'$_SERVER',
		'$_GET',
		'$_POST',
		'$_FILES',
		'$_COOKIE',
		'$_SESSION',
		'$_REQUEST',
		'$_ENV',
	];

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_VARIABLE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (!in_array($tokens[$pointer]['content'], self::SUPER_GLOBALS, true)) {
			return;
		}

		$phpcsFile->addError('Use of super global variable is disallowed.', $pointer, self::CODE_DISALLOWED_SUPER_GLOBAL_VARIABLE);
	}

}
