<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Variables;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use const T_DOLLAR;
use const T_DOLLAR_OPEN_CURLY_BRACES;

class DisallowVariableVariableSniff implements Sniff
{

	public const CODE_DISALLOWED_VARIABLE_VARIABLE = 'DisallowedVariableVariable';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOLLAR,
			T_DOLLAR_OPEN_CURLY_BRACES,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$phpcsFile->addError('Use of variable variable is disallowed.', $pointer, self::CODE_DISALLOWED_VARIABLE_VARIABLE);
	}

}
