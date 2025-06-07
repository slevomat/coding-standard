<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ArrayHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class DisallowPartiallyKeyedSniff implements Sniff
{

	public const CODE_DISALLOWED_PARTIALLY_KEYED = 'DisallowedPartiallyKeyed';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::ARRAY_TOKEN_CODES;
	}

	public function process(File $phpcsFile, int $stackPointer): void
	{
		$keyValues = ArrayHelper::parse($phpcsFile, $stackPointer);

		if (!ArrayHelper::isKeyed($keyValues)) {
			return;
		}

		if (ArrayHelper::isKeyedAll($keyValues)) {
			return;
		}

		$phpcsFile->addError('Partially keyed array disallowed.', $stackPointer, self::CODE_DISALLOWED_PARTIALLY_KEYED);
	}

}
