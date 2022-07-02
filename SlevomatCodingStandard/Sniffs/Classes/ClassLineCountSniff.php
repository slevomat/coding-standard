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

class ClassLineCountSniff implements Sniff
{

	public const CODE_CLASS_LINE_COUNT = 'ClassLineCount';

	/** @var int */
	public $maxLinesLength = 250;

	/** @var bool */
	public $includeComments = false;

	/** @var bool */
	public $includeWhitespace = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return array_values(Tokens::$ooScopeTokens);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$flags = array_keys(array_filter([
			FunctionHelper::LINE_INCLUDE_COMMENT => $this->includeComments,
			FunctionHelper::LINE_INCLUDE_WHITESPACE => $this->includeWhitespace,
		]));
		$flags = array_reduce($flags, static function ($carry, $flag): int {
			return $carry | $flag;
		}, 0);

		$length = FunctionHelper::getLineCount($phpcsFile, $pointer, $flags);

		if ($length <= SniffSettingsHelper::normalizeInteger($this->maxLinesLength)) {
			return;
		}

		$errorMessage = sprintf('Your class is too long. Currently using %d lines. Can be up to %d lines.', $length, $this->maxLinesLength);

		$phpcsFile->addError($errorMessage, $pointer, self::CODE_CLASS_LINE_COUNT);
	}

}
