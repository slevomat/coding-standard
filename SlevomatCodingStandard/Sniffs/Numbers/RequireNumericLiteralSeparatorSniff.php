<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Numbers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use function preg_match;
use function strpos;
use const T_DNUMBER;
use const T_LNUMBER;

class RequireNumericLiteralSeparatorSniff implements Sniff
{

	public const CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR = 'RequiredNumericLiteralSeparator';

	public ?bool $enable = null;

	public int $minDigitsBeforeDecimalPoint = 4;

	public int $minDigitsAfterDecimalPoint = 4;

	public bool $ignoreOctalNumbers = true;

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
	 * @param int $numberPointer
	 */
	public function process(File $phpcsFile, $numberPointer): void
	{
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 70400);
		$this->minDigitsBeforeDecimalPoint = SniffSettingsHelper::normalizeInteger($this->minDigitsBeforeDecimalPoint);
		$this->minDigitsAfterDecimalPoint = SniffSettingsHelper::normalizeInteger($this->minDigitsAfterDecimalPoint);

		if (!$this->enable) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$number = $tokens[$numberPointer]['content'];

		if (strpos($tokens[$numberPointer]['content'], '_') !== false) {
			return;
		}

		if (
			$this->ignoreOctalNumbers
			&& preg_match('~^0[0-7]+$~', $number) === 1
		) {
			return;
		}

		$regexp = '~(?:^\\d{' . $this->minDigitsBeforeDecimalPoint . '}|\.\\d{' . $this->minDigitsAfterDecimalPoint . '})~';

		if (preg_match($regexp, $number) === 0) {
			return;
		}

		$phpcsFile->addError(
			'Use of numeric literal separator is required.',
			$numberPointer,
			self::CODE_REQUIRED_NUMERIC_LITERAL_SEPARATOR,
		);
	}

}
