<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use Exception;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException;
use function array_key_exists;
use function array_map;
use function constant;
use function count;
use function defined;
use function in_array;
use function sprintf;
use function strlen;
use function substr;
use function substr_count;
use const T_ANON_CLASS;
use const T_BREAK;
use const T_CASE;
use const T_CATCH;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSURE;
use const T_COLON;
use const T_COMMENT;
use const T_CONTINUE;
use const T_DEFAULT;
use const T_DO;
use const T_ELSE;
use const T_ELSEIF;
use const T_EQUAL;
use const T_FINALLY;
use const T_FOR;
use const T_FOREACH;
use const T_GOTO;
use const T_IF;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_TAG;
use const T_RETURN;
use const T_SEMICOLON;
use const T_SWITCH;
use const T_THROW;
use const T_TRY;
use const T_WHILE;
use const T_WHITESPACE;
use const T_YIELD;
use const T_YIELD_FROM;

class ControlStructureSpacingSniff implements Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE = 'IncorrectLinesCountBeforeControlStructure';
	public const CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE = 'IncorrectLinesCountBeforeFirstControlStructure';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE = 'IncorrectLinesCountAfterControlStructure';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE = 'IncorrectLinesCountAfterLastControlStructure';

	/** @var int */
	public $linesCountAroundControlStructure = 1;

	/** @var int */
	public $linesCountBeforeFirstControlStructure = 0;

	/** @var int */
	public $linesCountAfterLastControlStructure = 0;

	/** @var string[] */
	public $tokensToCheck = [];

	/** @var (string|int)[]|null */
	private $normalizedTokensToCheck;

	/**
	 * @return (int|string)[]
	 */
	private function getTokensToCheck(): array
	{
		if ($this->normalizedTokensToCheck === null) {
			$this->normalizedTokensToCheck = array_map(function (string $tokenCode) {
				if (!defined($tokenCode)) {
					throw new UndefinedKeywordTokenException($tokenCode);
				}
				return constant($tokenCode);
			}, SniffSettingsHelper::normalizeArray($this->tokensToCheck));

			if (count($this->normalizedTokensToCheck) === 0) {
				$this->normalizedTokensToCheck = [
					T_IF,
					T_DO,
					T_WHILE,
					T_FOR,
					T_FOREACH,
					T_SWITCH,
					T_TRY,
					T_GOTO,
					T_BREAK,
					T_CONTINUE,
					T_RETURN,
					T_THROW,
					T_YIELD,
					T_YIELD_FROM,
				];
			}
		}

		return $this->normalizedTokensToCheck;
	}

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return $this->getTokensToCheck();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $controlStructurePointer
	 */
	public function process(File $phpcsFile, $controlStructurePointer): void
	{
		if ($this->isWhilePartOfDo($phpcsFile, $controlStructurePointer)) {
			return;
		}

		if ($this->isYieldWithAssigment($phpcsFile, $controlStructurePointer)) {
			return;
		}

		if ($this->isYieldFromWithReturn($phpcsFile, $controlStructurePointer)) {
			return;
		}

		$this->checkLinesBefore($phpcsFile, $controlStructurePointer);
		$this->checkLinesAfter($phpcsFile, $controlStructurePointer);
	}

	private function checkLinesBefore(File $phpcsFile, int $controlStructurePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $pointerBefore */
		$pointerBefore = TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $controlStructurePointer - 1);
		$controlStructureStartPointer = $controlStructurePointer;

		if (
			in_array($tokens[$pointerBefore]['code'], Tokens::$commentTokens, true)
			&& $tokens[$pointerBefore]['line'] + 1 === $tokens[$controlStructurePointer]['line']
		) {
			$pointerBeforeComment = TokenHelper::findPreviousEffective($phpcsFile, $pointerBefore - 1);
			if ($tokens[$pointerBeforeComment]['line'] !== $tokens[$pointerBefore]['line']) {
				$controlStructureStartPointer = array_key_exists('comment_opener', $tokens[$pointerBefore])
					? $tokens[$pointerBefore]['comment_opener']
					: $this->getMultilineCommentStartPointer($phpcsFile, $pointerBefore);
				/** @var int $pointerBefore */
				$pointerBefore = TokenHelper::findPreviousEffective($phpcsFile, $pointerBefore - 1);
			}
		}

		$isFirstControlStructure = in_array($tokens[$pointerBefore]['code'], [T_OPEN_CURLY_BRACKET, T_COLON], true);

		$whitespaceBefore = '';

		if ($tokens[$pointerBefore]['code'] === T_OPEN_TAG) {
			$whitespaceBefore .= substr($tokens[$pointerBefore]['content'], strlen('<?php'));
		}

		$hasCommentWithLineEndBefore = $tokens[$pointerBefore]['code'] === T_COMMENT && substr($tokens[$pointerBefore]['content'], -strlen($phpcsFile->eolChar)) === $phpcsFile->eolChar;
		if ($hasCommentWithLineEndBefore) {
			$whitespaceBefore .= $phpcsFile->eolChar;
		}

		if ($pointerBefore + 1 !== $controlStructurePointer) {
			$whitespaceBefore .= TokenHelper::getContent($phpcsFile, $pointerBefore + 1, $controlStructureStartPointer - 1);
		}

		$requiredLinesCountBefore = SniffSettingsHelper::normalizeInteger($isFirstControlStructure ? $this->linesCountBeforeFirstControlStructure : $this->linesCountAroundControlStructure);
		$actualLinesCountBefore = substr_count($whitespaceBefore, $phpcsFile->eolChar) - 1;

		if ($requiredLinesCountBefore === $actualLinesCountBefore) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('Expected %d lines before "%s", found %d.', $requiredLinesCountBefore, $tokens[$controlStructurePointer]['content'], $actualLinesCountBefore),
			$controlStructurePointer,
			$isFirstControlStructure ? self::CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE : self::CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		if ($tokens[$pointerBefore]['code'] === T_OPEN_TAG) {
			$phpcsFile->fixer->replaceToken($pointerBefore, '<?php');
		}

		$endOfLineBeforePointer = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, $phpcsFile->eolChar, $controlStructureStartPointer - 1);

		for ($i = $pointerBefore + 1; $i <= $endOfLineBeforePointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$linesToAdd = $hasCommentWithLineEndBefore ? $requiredLinesCountBefore - 1 : $requiredLinesCountBefore;
		for ($i = 0; $i <= $linesToAdd; $i++) {
			$phpcsFile->fixer->addNewline($pointerBefore);
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function checkLinesAfter(File $phpcsFile, int $controlStructurePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$controlStructureEndPointer = $this->findControlStructureEnd($phpcsFile, $controlStructurePointer);
		$notWhitespacePointerAfter = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $controlStructureEndPointer + 1);

		if ($notWhitespacePointerAfter === null) {
			return;
		}

		$hasCommentAfter = in_array($tokens[$notWhitespacePointerAfter]['code'], Tokens::$commentTokens, true) && $tokens[$notWhitespacePointerAfter]['line'] === $tokens[$controlStructureEndPointer]['line'];
		$pointerAfter = $hasCommentAfter ? TokenHelper::findNextEffective($phpcsFile, $controlStructureEndPointer + 1) : $notWhitespacePointerAfter;

		$isLastControlStructure = in_array($tokens[$controlStructurePointer]['code'], [T_CASE, T_DEFAULT], true)
			? $tokens[$pointerAfter]['code'] === T_CLOSE_CURLY_BRACKET
			: in_array($tokens[$pointerAfter]['code'], [T_CLOSE_CURLY_BRACKET, T_CASE, T_DEFAULT], true);

		$requiredLinesCountAfter = SniffSettingsHelper::normalizeInteger($isLastControlStructure ? $this->linesCountAfterLastControlStructure : $this->linesCountAroundControlStructure);
		$actualLinesCountAfter = $tokens[$pointerAfter]['line'] - $tokens[$controlStructureEndPointer]['line'] - 1;

		if ($requiredLinesCountAfter === $actualLinesCountAfter) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('Expected %d lines after "%s", found %d.', $requiredLinesCountAfter, $tokens[$controlStructurePointer]['content'], $actualLinesCountAfter),
			$controlStructurePointer,
			$isLastControlStructure ? self::CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE : self::CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$replaceStartPointer = $hasCommentAfter ? $notWhitespacePointerAfter : $controlStructureEndPointer;
		$endOfLineBeforeAfterPointer = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, $phpcsFile->eolChar, $pointerAfter - 1);

		for ($i = $replaceStartPointer + 1; $i <= $endOfLineBeforeAfterPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		if ($hasCommentAfter) {
			for ($i = 0; $i < $requiredLinesCountAfter; $i++) {
				$phpcsFile->fixer->addNewline($notWhitespacePointerAfter);
			}
		} else {
			for ($i = 0; $i <= $requiredLinesCountAfter; $i++) {
				$phpcsFile->fixer->addNewline($controlStructureEndPointer);
			}
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function getMultilineCommentStartPointer(File $phpcsFile, int $commentEndPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$commentStartPointer = $commentEndPointer;
		do {
			$commentBefore = TokenHelper::findPrevious($phpcsFile, T_COMMENT, $commentStartPointer - 1);
			if ($commentBefore === null) {
				break;
			}
			if ($tokens[$commentBefore]['line'] + 1 !== $tokens[$commentStartPointer]['line']) {
				break;
			}

			/** @var int $commentStartPointer */
			$commentStartPointer = $commentBefore;
		} while (true);

		return $commentStartPointer;
	}

	private function findControlStructureEnd(File $phpcsFile, int $controlStructurePointer): int
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$controlStructurePointer]['code'] === T_IF) {
			if (!array_key_exists('scope_closer', $tokens[$controlStructurePointer])) {
				throw new Exception('"if" without curly braces is not supported.');
			}

			$controlStructureEndPointer = $tokens[$controlStructurePointer]['scope_closer'];
			do {
				$nextPointer = TokenHelper::findNextEffective($phpcsFile, $controlStructureEndPointer + 1);
				if ($nextPointer === null) {
					return $controlStructureEndPointer;
				}

				if ($tokens[$nextPointer]['code'] === T_ELSE) {
					if (!array_key_exists('scope_closer', $tokens[$nextPointer])) {
						throw new Exception('"else" without curly braces is not supported.');
					}

					return $tokens[$nextPointer]['scope_closer'];
				}

				if ($tokens[$nextPointer]['code'] !== T_ELSEIF) {
					return $controlStructureEndPointer;
				}

				$controlStructureEndPointer = $tokens[$nextPointer]['scope_closer'];
			} while (true);
		}

		if ($tokens[$controlStructurePointer]['code'] === T_DO) {
			$whilePointer = TokenHelper::findNext($phpcsFile, T_WHILE, $tokens[$controlStructurePointer]['scope_closer'] + 1);
			return (int) TokenHelper::findNext($phpcsFile, T_SEMICOLON, $tokens[$whilePointer]['parenthesis_closer'] + 1);
		}

		if ($tokens[$controlStructurePointer]['code'] === T_TRY) {
			$controlStructureEndPointer = $tokens[$controlStructurePointer]['scope_closer'];
			do {
				$nextPointer = TokenHelper::findNextEffective($phpcsFile, $controlStructureEndPointer + 1);

				if ($nextPointer === null) {
					return $controlStructureEndPointer;
				}

				if (!in_array($tokens[$nextPointer]['code'], [T_CATCH, T_FINALLY], true)) {
					return $controlStructureEndPointer;
				}

				$controlStructureEndPointer = $tokens[$nextPointer]['scope_closer'];
			} while (true);
		}

		if (in_array($tokens[$controlStructurePointer]['code'], [T_WHILE, T_FOR, T_FOREACH, T_SWITCH], true)) {
			return $tokens[$controlStructurePointer]['scope_closer'];
		}

		if (in_array($tokens[$controlStructurePointer]['code'], [T_CASE, T_DEFAULT], true)) {
			$switchPointer = TokenHelper::findPrevious($phpcsFile, T_SWITCH, $controlStructurePointer - 1);
			$pointerAfterControlStructureEnd = TokenHelper::findNext($phpcsFile, [T_CASE, T_DEFAULT], $controlStructurePointer + 1, $tokens[$switchPointer]['scope_closer']);

			if ($pointerAfterControlStructureEnd === null) {
				return TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $tokens[$switchPointer]['scope_closer'] - 1);
			}

			return TokenHelper::findPreviousExcluding($phpcsFile, T_WHITESPACE, $pointerAfterControlStructureEnd - 1);
		}

		$nextPointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_ANON_CLASS, T_CLOSURE, T_OPEN_SHORT_ARRAY], $controlStructurePointer + 1);
		if ($tokens[$nextPointer]['code'] === T_SEMICOLON) {
			return $nextPointer;
		}

		if ($tokens[$nextPointer]['code'] === T_OPEN_SHORT_ARRAY) {
			return (int) TokenHelper::findNext($phpcsFile, T_SEMICOLON, $tokens[$nextPointer]['bracket_closer'] + 1);
		}

		return (int) TokenHelper::findNext($phpcsFile, T_SEMICOLON, $tokens[$nextPointer]['scope_closer'] + 1);
	}

	private function isWhilePartOfDo(File $phpcsFile, int $controlStructurePointer): bool
	{
		$tokens = $phpcsFile->getTokens();
		$pointerBefore = TokenHelper::findPreviousEffective($phpcsFile, $controlStructurePointer - 1);

		return
			$tokens[$controlStructurePointer]['code'] === T_WHILE
			&& $tokens[$pointerBefore]['code'] === T_CLOSE_CURLY_BRACKET
			&& array_key_exists('scope_condition', $tokens[$pointerBefore])
			&& $tokens[$tokens[$pointerBefore]['scope_condition']]['code'] === T_DO;
	}

	private function isYieldWithAssigment(File $phpcsFile, int $controlStructurePointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$controlStructurePointer]['code'] !== T_YIELD) {
			return false;
		}

		$pointerBefore = TokenHelper::findPreviousEffective($phpcsFile, $controlStructurePointer - 1);
		return $tokens[$pointerBefore]['code'] === T_EQUAL;
	}

	private function isYieldFromWithReturn(File $phpcsFile, int $controlStructurePointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$controlStructurePointer]['code'] !== T_YIELD_FROM) {
			return false;
		}

		$pointerBefore = TokenHelper::findPreviousEffective($phpcsFile, $controlStructurePointer - 1);
		return $tokens[$pointerBefore]['code'] === T_RETURN;
	}

}
