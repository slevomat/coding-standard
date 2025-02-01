<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function sprintf;
use function str_repeat;
use const T_ATTRIBUTE;
use const T_FUNCTION;
use const T_SEMICOLON;

class MethodSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS = 'IncorrectLinesCountBetweenMethods';

	public int $minLinesCount = 1;

	public int $maxLinesCount = 1;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_FUNCTION];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $methodPointer
	 */
	public function process(File $phpcsFile, $methodPointer): void
	{
		$this->minLinesCount = SniffSettingsHelper::normalizeInteger($this->minLinesCount);
		$this->maxLinesCount = SniffSettingsHelper::normalizeInteger($this->maxLinesCount);

		if (!FunctionHelper::isMethod($phpcsFile, $methodPointer)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$methodEndPointer = array_key_exists('scope_closer', $tokens[$methodPointer])
			? $tokens[$methodPointer]['scope_closer']
			: TokenHelper::findNext($phpcsFile, T_SEMICOLON, $methodPointer + 1);

		$classPointer = ClassHelper::getClassPointer($phpcsFile, $methodPointer);

		$nextMethodPointer = TokenHelper::findNext($phpcsFile, T_FUNCTION, $methodEndPointer + 1, $tokens[$classPointer]['scope_closer']);
		if ($nextMethodPointer === null) {
			return;
		}

		$nextMethodAttributeStartPointer = null;
		$nextMethodDocCommentStartPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $nextMethodPointer);

		if (
			$nextMethodDocCommentStartPointer !== null
			&& $tokens[$tokens[$nextMethodDocCommentStartPointer]['comment_closer']]['line'] + 1 !== $tokens[$nextMethodPointer]['line']
		) {
			$nextMethodDocCommentStartPointer = null;
		} else {
			$nextMethodAttributeStartPointer = TokenHelper::findPrevious(
				$phpcsFile,
				T_ATTRIBUTE,
				$nextMethodPointer - 1,
				$methodEndPointer,
			);
		}

		$nextMethodFirstLinePointer = $tokens[$nextMethodPointer]['line'] === $tokens[$methodEndPointer]['line']
			? TokenHelper::findNextEffective($phpcsFile, $methodEndPointer + 1)
			: TokenHelper::findFirstTokenOnLine(
				$phpcsFile,
				$nextMethodDocCommentStartPointer ?? $nextMethodAttributeStartPointer ?? $nextMethodPointer,
			);

		if (TokenHelper::findNextNonWhitespace($phpcsFile, $methodEndPointer + 1, $nextMethodFirstLinePointer) !== null) {
			return;
		}

		$linesBetween = $tokens[$nextMethodFirstLinePointer]['line'] !== $tokens[$methodEndPointer]['line']
			? $tokens[$nextMethodFirstLinePointer]['line'] - $tokens[$methodEndPointer]['line'] - 1
			: null;

		if ($linesBetween !== null && $linesBetween >= $this->minLinesCount && $linesBetween <= $this->maxLinesCount) {
			return;
		}

		if ($this->minLinesCount === $this->maxLinesCount) {
			$errorMessage = $this->minLinesCount === 1
				? 'Expected 1 blank line after method, found %3$d.'
				: 'Expected %2$d blank lines after method, found %3$d.';
		} else {
			$errorMessage = 'Expected %1$d to %2$d blank lines after method, found %3$d.';
		}

		$fix = $phpcsFile->addFixableError(
			sprintf($errorMessage, $this->minLinesCount, $this->maxLinesCount, $linesBetween ?? 0),
			$methodPointer,
			self::CODE_INCORRECT_LINES_COUNT_BETWEEN_METHODS,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($linesBetween === null) {
			$phpcsFile->fixer->addContent(
				$methodEndPointer,
				$phpcsFile->eolChar . str_repeat($phpcsFile->eolChar, $this->minLinesCount) . IndentationHelper::getIndentation(
					$phpcsFile,
					TokenHelper::findFirstNonWhitespaceOnLine($phpcsFile, $methodPointer),
				),
			);

			FixerHelper::removeBetween($phpcsFile, $methodEndPointer, $nextMethodFirstLinePointer);

		} elseif ($linesBetween > $this->maxLinesCount) {
			$phpcsFile->fixer->addContent($methodEndPointer, str_repeat($phpcsFile->eolChar, $this->maxLinesCount + 1));

			$firstPointerOnNextMethodLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $nextMethodFirstLinePointer);

			FixerHelper::removeBetween($phpcsFile, $methodEndPointer, $firstPointerOnNextMethodLine);
		} else {
			$phpcsFile->fixer->addContent($methodEndPointer, str_repeat($phpcsFile->eolChar, $this->minLinesCount - $linesBetween));
		}

		$phpcsFile->fixer->endChangeset();
	}

}
