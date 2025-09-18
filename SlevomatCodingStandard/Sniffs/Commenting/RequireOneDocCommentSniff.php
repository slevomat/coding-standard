<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use function implode;
use function preg_match;
use function preg_replace;
use function preg_split;
use function strpos;
use function trim;
use const T_COMMENT;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_WHITESPACE;

class RequireOneDocCommentSniff implements Sniff
{

	public const CODE_MULTIPLE_DOC_COMMENTS = 'MultipleDocComments';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_DOC_COMMENT_OPEN_TAG];
	}

	public function process(File $phpcsFile, int $stackPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$firstOpener = $stackPointer;
		$firstCloser = $tokens[$firstOpener]['comment_closer'];

		/** @var int $next */
		$next = $phpcsFile->findNext([T_WHITESPACE], $firstCloser + 1, null, true);

		if ($tokens[$next]['code'] === T_COMMENT) {
			return;
		}

		if ($tokens[$next]['code'] !== T_DOC_COMMENT_OPEN_TAG) {
			return;
		}

		$secondOpener = $next;
		$secondCloser = $tokens[$secondOpener]['comment_closer'];

		if ($this->isSingleLineVarDoc($phpcsFile, $firstOpener, $firstCloser)
			&& $this->isSingleLineVarDoc($phpcsFile, $secondOpener, $secondCloser)) {
			return;
		}

		$error = 'Only one PHPDoc comment is allowed for a documentable entity; found two adjacent doc comments.';
		$phpcsFile->addError($error, $firstOpener, self::CODE_MULTIPLE_DOC_COMMENTS);
	}

	private function isSingleLineVarDoc(File $phpcsFile, int $opener, int $closer): bool
	{
		$length = $closer - $opener + 1;
		$text = $phpcsFile->getTokensAsString($opener, $length);

		if ((strpos($text, "\n") !== false) || (strpos($text, "\r") !== false)) {
			return false;
		}

		$inner = $this->getDocInner($text);

		return preg_match('/^@var\b/i', $inner) === 1 && preg_match('/\$\w+\s*$/', $inner) === 1;
	}

	private function getDocInner(string $doc): string
	{
		$doc = preg_replace('#\A\s*/\*\*\s*#s', '', $doc);
		$doc = preg_replace('#\s*\*/\s*\z#s', '', $doc);

		/** @var list<string> $lines */
		$lines = preg_split("/\r\n|\n|\r/", $doc);

		$clean = [];

		foreach ($lines as $line) {
			$line = preg_replace('#^\s*\*\s?#', '', $line);
			$clean[] = $line;
		}

		$inner = implode("\n", $clean);

		return trim($inner, "\n\r ");
	}

}
