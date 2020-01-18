<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function assert;
use function in_array;
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
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		if (!FunctionHelper::isMethod($phpcsFile, $pointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$lineStartPointer = $phpcsFile->findFirstOnLine(T_OPEN_TAG, $pointer, true);
		assert(!is_bool($lineStartPointer));

		$methodSignatureEndPointer = $phpcsFile->findNext([T_OPEN_CURLY_BRACKET, T_SEMICOLON], $pointer);
		assert(is_int($methodSignatureEndPointer));

		$signatureEndLine = $tokens[$methodSignatureEndPointer]['line'];
		if (in_array($tokens[$lineStartPointer]['line'], [$signatureEndLine, $signatureEndLine - 1], true)) {
			return;
		}

		$singleLineMethodSignatureEndPointer = $methodSignatureEndPointer;
		if ($tokens[$methodSignatureEndPointer]['code'] === T_OPEN_CURLY_BRACKET) {
			$singleLineMethodSignatureEndPointer--;
		}

		$methodSignature = TokenHelper::getContent($phpcsFile, $lineStartPointer, $singleLineMethodSignatureEndPointer);
		$methodSignature = preg_replace(sprintf('~%s[ \t]*~', $phpcsFile->eolChar), ' ', $methodSignature);
		assert(is_string($methodSignature));

		$methodSignature = str_replace(['( ', ' )'], ['(', ')'], $methodSignature);
		$methodSignature = rtrim($methodSignature);

		$methodSignatureWithoutTabIndetation = preg_replace_callback('~^(\t+)~', static function (array $matches): string {
			return str_repeat('    ', strlen($matches[1]));
		}, $methodSignature);

		if ($this->maxLineLength !== 0 && strlen($methodSignatureWithoutTabIndetation) > $this->maxLineLength) {
			return;
		}

		$error = sprintf('Signature of method "%s" should be placed on a single line.', FunctionHelper::getName($phpcsFile, $pointer));
		$fix = $phpcsFile->addFixableError($error, $pointer, self::CODE_REQUIRED_SINGLE_LINE_SIGNATURE);
		if (!$fix) {
			return;
		}

		$whitespaceBeforeMethod = $tokens[$lineStartPointer]['content'];

		$phpcsFile->fixer->beginChangeset();

		for ($i = $lineStartPointer; $i <= $methodSignatureEndPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$replacement = $methodSignature;
		if ($tokens[$methodSignatureEndPointer]['code'] === T_OPEN_CURLY_BRACKET) {
			$replacement = sprintf("%s\n%s{", $methodSignature, $whitespaceBeforeMethod);
		}

		$phpcsFile->fixer->replaceToken($lineStartPointer, $replacement);

		$phpcsFile->fixer->endChangeset();
	}

}
