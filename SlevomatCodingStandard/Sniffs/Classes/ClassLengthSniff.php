<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use function array_filter;
use function array_keys;
use function array_reduce;
use function array_values;
use function sprintf;

class ClassLengthSniff implements Sniff
{

	public const CODE_CLASS_TOO_LONG = 'ClassTooLong';

	public int $maxLinesLength = 250;

	public bool $includeComments = false;

	public bool $includeWhitespace = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return array_values(Tokens::$ooScopeTokens);
	}

	public function process(File $phpcsFile, int $pointer): void
	{
		$this->maxLinesLength = SniffSettingsHelper::normalizeInteger($this->maxLinesLength);
		$flags = array_keys(array_filter([
			FunctionHelper::LINE_INCLUDE_COMMENT => $this->includeComments,
			FunctionHelper::LINE_INCLUDE_WHITESPACE => $this->includeWhitespace,
		]));
		$flags = array_reduce($flags, static fn ($carry, $flag): int => $carry | $flag, 0);

		$length = FunctionHelper::getLineCount($phpcsFile, $pointer, $flags);

		if ($length <= $this->maxLinesLength) {
			return;
		}

		$errorMessage = sprintf('Your class is too long. Currently using %d lines. Can be up to %d lines.', $length, $this->maxLinesLength);

		$phpcsFile->addError($errorMessage, $pointer, self::CODE_CLASS_TOO_LONG);
	}

}
