<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function assert;
use function is_bool;
use function is_int;
use function is_string;
use function preg_replace;
use function preg_replace_callback;
use function rtrim;
use function sprintf;
use function str_repeat;
use function str_replace;
use function strlen;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_TAG;
use const T_SEMICOLON;

class RequireSingleLineMethodSignatureSniff implements Sniff
{

	public const CODE_REQUIRED_SINGLE_LINE_SIGNATURE = 'RequiredSingleLineSignature';

	/** @var int */
	public $maxLineLength = 120;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_FUNCTION];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $methodPointer
	 */
	public function process(File $phpcsFile, $methodPointer): void
	{
		if (!FunctionHelper::isMethod($phpcsFile, $methodPointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$signatureStartPointer = $phpcsFile->findFirstOnLine(T_OPEN_TAG, $methodPointer, true);
		assert(!is_bool($signatureStartPointer));

		$pointerAfterSignatureEnd = TokenHelper::findNext($phpcsFile, [T_OPEN_CURLY_BRACKET, T_SEMICOLON], $methodPointer + 1);

		$signatureEndPointer = TokenHelper::findPreviousEffective($phpcsFile, $pointerAfterSignatureEnd - 1);
		assert(is_int($signatureEndPointer));

		if ($tokens[$signatureStartPointer]['line'] === $tokens[$signatureEndPointer]['line']) {
			return;
		}

		$methodSignature = TokenHelper::getContent($phpcsFile, $signatureStartPointer, $signatureEndPointer);
		$methodSignature = preg_replace(sprintf('~%s[ \t]*~', $phpcsFile->eolChar), ' ', $methodSignature);
		assert(is_string($methodSignature));

		$methodSignature = str_replace(['( ', ' )'], ['(', ')'], $methodSignature);
		$methodSignature = rtrim($methodSignature);

		$methodSignatureWithoutTabIndetation = preg_replace_callback('~^(\t+)~', static function (array $matches): string {
			return str_repeat('    ', strlen($matches[1]));
		}, $methodSignature);

		$maxLineLength = SniffSettingsHelper::normalizeInteger($this->maxLineLength);
		if ($maxLineLength !== 0 && strlen($methodSignatureWithoutTabIndetation) > SniffSettingsHelper::normalizeInteger($maxLineLength)) {
			return;
		}

		$error = sprintf('Signature of method "%s" should be placed on a single line.', FunctionHelper::getName($phpcsFile, $methodPointer));
		$fix = $phpcsFile->addFixableError($error, $methodPointer, self::CODE_REQUIRED_SINGLE_LINE_SIGNATURE);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->replaceToken($signatureStartPointer, $methodSignature);

		for ($i = $signatureStartPointer + 1; $i <= $signatureEndPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->endChangeset();
	}

}
