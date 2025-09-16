<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function preg_match;
use function rtrim;
use function sprintf;
use function strlen;
use function substr;
use function substr_count;
use const T_NAMESPACE;
use const T_OPEN_TAG;
use const T_SEMICOLON;

class NamespaceSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE = 'IncorrectLinesCountBeforeNamespace';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_NAMESPACE = 'IncorrectLinesCountAfterNamespace';

	public int $linesCountBeforeNamespace = 1;

	public int $linesCountAfterNamespace = 1;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_NAMESPACE,
		];
	}

	public function process(File $phpcsFile, int $namespacePointer): void
	{
		$this->linesCountBeforeNamespace = SniffSettingsHelper::normalizeInteger($this->linesCountBeforeNamespace);
		$this->linesCountAfterNamespace = SniffSettingsHelper::normalizeInteger($this->linesCountAfterNamespace);

		$this->checkLinesBeforeNamespace($phpcsFile, $namespacePointer);
		$this->checkLinesAfterNamespace($phpcsFile, $namespacePointer);
	}

	private function checkLinesBeforeNamespace(File $phpcsFile, int $namespacePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $pointerBeforeNamespace */
		$pointerBeforeNamespace = TokenHelper::findPreviousNonWhitespace($phpcsFile, $namespacePointer - 1);

		$whitespaceBeforeNamespace = '';

		$isInlineCommentBefore = (bool) preg_match('~^(?://|#)(.*)~', $tokens[$pointerBeforeNamespace]['content']);

		if ($tokens[$pointerBeforeNamespace]['code'] === T_OPEN_TAG) {
			$whitespaceBeforeNamespace .= substr($tokens[$pointerBeforeNamespace]['content'], strlen('<?php'));
		} elseif ($isInlineCommentBefore) {
			$whitespaceBeforeNamespace .= $phpcsFile->eolChar;
		}

		if ($pointerBeforeNamespace + 1 !== $namespacePointer) {
			$whitespaceBeforeNamespace .= TokenHelper::getContent($phpcsFile, $pointerBeforeNamespace + 1, $namespacePointer - 1);
		}

		$actualLinesCountBeforeNamespace = substr_count($whitespaceBeforeNamespace, $phpcsFile->eolChar) - 1;

		if ($actualLinesCountBeforeNamespace === $this->linesCountBeforeNamespace) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s before namespace statement, found %d.',
				$this->linesCountBeforeNamespace,
				$this->linesCountBeforeNamespace === 1 ? '' : 's',
				$actualLinesCountBeforeNamespace,
			),
			$namespacePointer,
			self::CODE_INCORRECT_LINES_COUNT_BEFORE_NAMESPACE,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($tokens[$pointerBeforeNamespace]['code'] === T_OPEN_TAG) {
			FixerHelper::replace($phpcsFile, $pointerBeforeNamespace, '<?php');
		} elseif ($isInlineCommentBefore) {
			FixerHelper::replace(
				$phpcsFile,
				$pointerBeforeNamespace,
				rtrim($tokens[$pointerBeforeNamespace]['content'], $phpcsFile->eolChar),
			);
		}

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeNamespace, $namespacePointer);

		for ($i = 0; $i <= $this->linesCountBeforeNamespace; $i++) {
			$phpcsFile->fixer->addNewline($pointerBeforeNamespace);
		}
		$phpcsFile->fixer->endChangeset();
	}

	private function checkLinesAfterNamespace(File $phpcsFile, int $namespacePointer): void
	{
		if (array_key_exists('scope_opener', $phpcsFile->getTokens()[$namespacePointer])) {
			return;
		}

		/** @var int $namespaceSemicolonPointer */
		$namespaceSemicolonPointer = TokenHelper::findNextLocal($phpcsFile, T_SEMICOLON, $namespacePointer + 1);

		$pointerAfterWhitespaceEnd = TokenHelper::findNextNonWhitespace($phpcsFile, $namespaceSemicolonPointer + 1);
		if ($pointerAfterWhitespaceEnd === null) {
			return;
		}

		$whitespaceAfterNamespace = TokenHelper::getContent($phpcsFile, $namespaceSemicolonPointer + 1, $pointerAfterWhitespaceEnd - 1);

		$actualLinesCountAfterNamespace = substr_count($whitespaceAfterNamespace, $phpcsFile->eolChar) - 1;

		if ($actualLinesCountAfterNamespace === $this->linesCountAfterNamespace) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s after namespace statement, found %d.',
				$this->linesCountAfterNamespace,
				$this->linesCountAfterNamespace === 1 ? '' : 's',
				$actualLinesCountAfterNamespace,
			),
			$namespacePointer,
			self::CODE_INCORRECT_LINES_COUNT_AFTER_NAMESPACE,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $namespaceSemicolonPointer, $pointerAfterWhitespaceEnd);

		for ($i = 0; $i <= $this->linesCountAfterNamespace; $i++) {
			$phpcsFile->fixer->addNewline($namespaceSemicolonPointer);
		}
		$phpcsFile->fixer->endChangeset();
	}

}
