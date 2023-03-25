<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function in_array;
use function ltrim;
use function rtrim;
use function strpos;
use function trim;
use const T_COMMA;
use const T_DOUBLE_ARROW;
use const T_WHITESPACE;

/**
 * @internal
 */
class ArrayKeyValue
{

	/** @var int */
	private $pointerStart;

	/** @var int */
	private $pointerEnd;

	/** @var ?string */
	private $indent = null;

	/** @var ?string */
	private $key = null;

	/** @var ?int */
	private $pointerArrow = null;

	/** @var ?int */
	private $pointerComma = null;

	public function __construct(File $phpcsFile, int $pointerStart, int $pointerEnd)
	{
		$this->pointerStart = $pointerStart;
		$this->pointerEnd = $pointerEnd;
		$this->addValues($phpcsFile);
	}

	public function getContent(File $phpcsFile, bool $normalize = false, ?string $indent = null): string
	{
		if ($normalize === false) {
			return TokenHelper::getContent($phpcsFile, $this->pointerStart, $this->pointerEnd);
		}
		$content = '';
		$addCommaPtr = $this->pointerComma === null
			? TokenHelper::findPreviousEffective($phpcsFile, $this->pointerEnd)
			: null;
		$tokens = $phpcsFile->getTokens();
		for ($pointer = $this->pointerStart; $pointer <= $this->pointerEnd; $pointer++) {
			$token = $tokens[$pointer];
			$content .= $token['content'];
			if ($pointer === $addCommaPtr) {
				$content .= ',';
			}
		}

		// Trim, but keep leading empty lines
		$content = ltrim($content, " \t");
		$content = rtrim($content);

		if ($indent !== null && strpos($content, $phpcsFile->eolChar) !== 0) {
			$content = $indent . $content;
		}

		return $content;
	}

	public function getIndent(): ?string
	{
		return $this->indent;
	}

	public function getKey(): ?string
	{
		return $this->key;
	}

	public function getPointerArrow(): ?int
	{
		return $this->pointerArrow;
	}

	public function getPointerComma(): ?int
	{
		return $this->pointerComma;
	}

	public function getPointerEnd(): int
	{
		return $this->pointerEnd;
	}

	public function getPointerStart(): int
	{
		return $this->pointerStart;
	}

	private function addValues(File $phpcsFile): void
	{
		$key = '';
		$tokens = $phpcsFile->getTokens();
		$firstNonWhitespace = null;
		$pointerCloser = null;
		for ($pointer = $this->pointerStart; $pointer <= $this->pointerEnd; $pointer++) {
			if ($pointer < $pointerCloser) {
				continue;
			}
			$token = $tokens[$pointer];
			if (in_array($token['code'], TokenHelper::$arrayTokenCodes, true)) {
				$pointerCloser = ArrayHelper::openClosePointers($token)[1];
			} elseif ($token['code'] === T_DOUBLE_ARROW) {
				$this->pointerArrow = $pointer;
			} elseif ($token['code'] === T_COMMA) {
				$this->pointerComma = $pointer;
			} elseif ($this->pointerArrow === null) {
				if ($firstNonWhitespace === null && $token['code'] !== T_WHITESPACE) {
					$firstNonWhitespace = $pointer;
				}
				if (in_array($token['code'], TokenHelper::$inlineCommentTokenCodes, true) === false) {
					$key .= $token['content'];
				}
			}
		}
		$haveIndent = $firstNonWhitespace !== null && TokenHelper::findFirstNonWhitespaceOnLine(
			$phpcsFile,
			$firstNonWhitespace
		) === $firstNonWhitespace;
		$this->indent = $haveIndent
			? TokenHelper::getContent(
				$phpcsFile,
				TokenHelper::findFirstTokenOnLine($phpcsFile, $firstNonWhitespace),
				$firstNonWhitespace - 1
			)
			: null;
		$this->key = $this->pointerArrow !== null
			? trim($key)
			: null;
	}

}
