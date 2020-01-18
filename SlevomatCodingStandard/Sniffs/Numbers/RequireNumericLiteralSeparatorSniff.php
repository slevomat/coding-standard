<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Numbers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use function preg_match;
use function strpos;
use const T_DNUMBER;
use const T_LNUMBER;

class RequireNumericLiteralSeparatorSniff implements Sniff
{

	public const CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR = 'RequiredNumericLiteralSeparator';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_LNUMBER,
			T_DNUMBER,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $numberPointer
	 */
	public function process(File $phpcsFile, $numberPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (strpos($tokens[$numberPointer]['content'], '_') !== false) {
			return;
		}

		if (preg_match('~(?:^\\d{4}|\.\\d{4})~', $tokens[$numberPointer]['content']) === 0) {
			return;
		}

		$phpcsFile->addError('Use of numeric literator separator is required.', $numberPointer, self::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR);
	}

}
