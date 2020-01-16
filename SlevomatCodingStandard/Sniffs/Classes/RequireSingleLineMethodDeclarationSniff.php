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
use function rtrim;
use function sprintf;
use function str_replace;
use function strlen;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_TAG;
use const T_SEMICOLON;

class RequireSingleLineMethodDeclarationSniff implements Sniff
{

	public const CODE_UNNECESSARY_MULTI_LINE_METHOD = 'UnnecessaryMultiLineMethod';

	/** @var int */
	public $maxLineLength = 120;

	/** @return int[] */
	public function register(): array
	{
		return [T_FUNCTION];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $file
	 * @param int $pointer
	 */
	public function process(File $file, $pointer): void
	{
		if (!FunctionHelper::isMethod($file, $pointer)) {
			return;
		}

		$tokens = $file->getTokens();

		$lineStartPointer = $file->findFirstOnLine(T_OPEN_TAG, $pointer, true);
		assert(!is_bool($lineStartPointer));

		$methodDeclarationEndPointer = $file->findNext([T_OPEN_CURLY_BRACKET, T_SEMICOLON], $pointer);
		assert(is_int($methodDeclarationEndPointer));

		$declarationEndLine = $tokens[$methodDeclarationEndPointer]['line'];
		if (in_array($tokens[$lineStartPointer]['line'], [$declarationEndLine, $declarationEndLine - 1], true)) {
			return;
		}

		$singleLineMethodDeclarationEndPointer = $methodDeclarationEndPointer;
		if ($tokens[$methodDeclarationEndPointer]['code'] === T_OPEN_CURLY_BRACKET) {
			$singleLineMethodDeclarationEndPointer--;
		}

		$methodDeclaration = TokenHelper::getContent($file, $lineStartPointer, $singleLineMethodDeclarationEndPointer);
		$methodDeclaration = preg_replace(sprintf('~%s[ \t]*~', $file->eolChar), ' ', $methodDeclaration);
		assert(is_string($methodDeclaration));

		$methodDeclaration = str_replace(['( ', ' )'], ['(', ')'], $methodDeclaration);
		$methodDeclaration = rtrim($methodDeclaration);

		if ($this->maxLineLength !== 0 && strlen($methodDeclaration) > $this->maxLineLength) {
			return;
		}

		$error = sprintf('Method "%s" can be placed on a single line.', FunctionHelper::getName($file, $pointer));
		$fix = $file->addFixableError($error, $pointer, self::CODE_UNNECESSARY_MULTI_LINE_METHOD);
		if (!$fix) {
			return;
		}

		$whitespaceBeforeMethod = $tokens[$lineStartPointer]['content'];

		$file->fixer->beginChangeset();

		for ($i = $lineStartPointer; $i <= $methodDeclarationEndPointer; $i++) {
			$file->fixer->replaceToken($i, '');
		}

		$replacement = $methodDeclaration;
		if ($tokens[$methodDeclarationEndPointer]['code'] === T_OPEN_CURLY_BRACKET) {
			$replacement = sprintf("%s\n%s{", $methodDeclaration, $whitespaceBeforeMethod);
		}

		$file->fixer->replaceToken($lineStartPointer, $replacement);

		$file->fixer->endChangeset();
	}

}
