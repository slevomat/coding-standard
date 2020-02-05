<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use Exception;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\CommentHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException;
use Throwable;
use function array_key_exists;
use function array_map;
use function array_values;
use function constant;
use function count;
use function defined;
use function in_array;
use function sprintf;
use function strlen;
use function substr;
use function substr_count;
use const T_ANON_CLASS;
use const T_CASE;
use const T_CATCH;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSURE;
use const T_COLON;
use const T_COMMENT;
use const T_DEFAULT;
use const T_DO;
use const T_ELSE;
use const T_ELSEIF;
use const T_FINALLY;
use const T_FN;
use const T_FOR;
use const T_FOREACH;
use const T_IF;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_TAG;
use const T_SEMICOLON;
use const T_SWITCH;
use const T_TRY;
use const T_WHILE;
use const T_WHITESPACE;

/**
 * @internal
 */
abstract class AbstractControlStructureSpacing implements Sniff
{

	public const CODE_INCORRECT_LINES_COUNT_BEFORE_CONTROL_STRUCTURE = 'IncorrectLinesCountBeforeControlStructure';
	public const CODE_INCORRECT_LINES_COUNT_BEFORE_FIRST_CONTROL_STRUCTURE = 'IncorrectLinesCountBeforeFirstControlStructure';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_CONTROL_STRUCTURE = 'IncorrectLinesCountAfterControlStructure';
	public const CODE_INCORRECT_LINES_COUNT_AFTER_LAST_CONTROL_STRUCTURE = 'IncorrectLinesCountAfterLastControlStructure';

	/** @var (string|int)[]|null */
	private $normalizedTokensToCheck;

	/**
	 * @return array<int|string>
	 */
	abstract protected function getSupportedTokens(): array;

	/**
	 * @return string[]
	 */
	abstract protected function getTokensToCheck(): array;

	abstract protected function getLinesCountBefore(): int;

	abstract protected function getLinesCountBeforeFirst(): int;

	abstract protected function getLinesCountAfter(): int;

	abstract protected function getLinesCountAfterLast(): int;

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return $this->getNormalizedTokensToCheck();
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $controlStructurePointer
	 */
	public function process(File $phpcsFile, $controlStructurePointer): void
	{
		$this->checkLinesBefore($phpcsFile, $controlStructurePointer);

		try {
			$this->checkLinesAfter($phpcsFile, $controlStructurePointer);
		} catch (Throwable $e) {
			// Unsupported syntax without curly braces.
			return;
		}
	}

	protected function checkLinesBefore(File $phpcsFile, int $controlStructurePointer): void
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
					: CommentHelper::getMultilineCommentStartPointer($phpcsFile, $pointerBefore);
				/** @var int $pointerBefore */
				$pointerBefore = $pointerBeforeComment;
			}
		}

		$isFirstControlStructure = in_array($tokens[$pointerBefore]['code'], [T_OPEN_CURLY_BRACKET, T_COLON], true);

		$whitespaceBefore = '';

		if ($tokens[$pointerBefore]['code'] === T_OPEN_TAG) {
			$whitespaceBefore .= substr($tokens[$pointerBefore]['content'], strlen('<?php'));
		}

		$hasCommentWithLineEndBefore = ($tokens[$pointerBefore]['code'] === T_COMMENT || in_array($tokens[$pointerBefore]['code'], Tokens::$phpcsCommentTokens, true))
			&& substr($tokens[$pointerBefore]['content'], -strlen($phpcsFile->eolChar)) === $phpcsFile->eolChar;
		if ($hasCommentWithLineEndBefore) {
			$whitespaceBefore .= $phpcsFile->eolChar;
		}

		if ($pointerBefore + 1 !== $controlStructurePointer) {
			$whitespaceBefore .= TokenHelper::getContent($phpcsFile, $pointerBefore + 1, $controlStructureStartPointer - 1);
		}

		$requiredLinesCountBefore = $isFirstControlStructure ? $this->getLinesCountBeforeFirst() : $this->getLinesCountBefore();
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

		$endOfLineBeforePointer = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, $phpcsFile->eolChar, $controlStructureStartPointer - 1);

		$phpcsFile->fixer->beginChangeset();

		if ($tokens[$pointerBefore]['code'] === T_OPEN_TAG) {
			$phpcsFile->fixer->replaceToken($pointerBefore, '<?php');
		}

		for ($i = $pointerBefore + 1; $i <= $endOfLineBeforePointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$linesToAdd = $hasCommentWithLineEndBefore ? $requiredLinesCountBefore - 1 : $requiredLinesCountBefore;
		for ($i = 0; $i <= $linesToAdd; $i++) {
			$phpcsFile->fixer->addNewline($pointerBefore);
		}

		$phpcsFile->fixer->endChangeset();
	}

	protected function checkLinesAfter(File $phpcsFile, int $controlStructurePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$controlStructureEndPointer = $this->findControlStructureEnd($phpcsFile, $controlStructurePointer);

		$pointerAfterControlStructureEnd = TokenHelper::findNextEffective($phpcsFile, $controlStructureEndPointer + 1);
		if ($pointerAfterControlStructureEnd !== null && $tokens[$pointerAfterControlStructureEnd]['code'] === T_SEMICOLON) {
			$controlStructureEndPointer = $pointerAfterControlStructureEnd;
		}

		$notWhitespacePointerAfter = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $controlStructureEndPointer + 1);

		if ($notWhitespacePointerAfter === null) {
			return;
		}

		$hasCommentAfter = in_array($tokens[$notWhitespacePointerAfter]['code'], Tokens::$commentTokens, true) && $tokens[$notWhitespacePointerAfter]['line'] === $tokens[$controlStructureEndPointer]['line'];
		$pointerAfter = $hasCommentAfter ? TokenHelper::findNextEffective($phpcsFile, $controlStructureEndPointer + 1) : $notWhitespacePointerAfter;

		$isLastControlStructure = in_array($tokens[$controlStructurePointer]['code'], [T_CASE, T_DEFAULT], true)
			? $tokens[$pointerAfter]['code'] === T_CLOSE_CURLY_BRACKET
			: in_array($tokens[$pointerAfter]['code'], [T_CLOSE_CURLY_BRACKET, T_CASE, T_DEFAULT], true);

		$requiredLinesCountAfter = $isLastControlStructure ? $this->getLinesCountAfterLast() : $this->getLinesCountAfter();
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

		$replaceStartPointer = $hasCommentAfter ? $notWhitespacePointerAfter : $controlStructureEndPointer;
		$endOfLineBeforeAfterPointer = TokenHelper::findPreviousContent($phpcsFile, T_WHITESPACE, $phpcsFile->eolChar, $pointerAfter - 1);

		$phpcsFile->fixer->beginChangeset();

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

	/**
	 * @return (int|string)[]
	 */
	private function getNormalizedTokensToCheck(): array
	{
		if ($this->normalizedTokensToCheck === null) {
			$supportedTokens = $this->getSupportedTokens();

			$this->normalizedTokensToCheck = array_values(array_map(
				static function (string $tokenCode) use ($supportedTokens) {
					if (!defined($tokenCode)) {
						throw new UndefinedKeywordTokenException($tokenCode);
					}

					$const = constant($tokenCode);
					if (!in_array($const, $supportedTokens, true)) {
						throw new UnsupportedTokenException($tokenCode);
					}

					return $const;
				},
				SniffSettingsHelper::normalizeArray($this->getTokensToCheck())
			));

			if (count($this->normalizedTokensToCheck) === 0) {
				$this->normalizedTokensToCheck = $supportedTokens;
			}
		}

		return $this->normalizedTokensToCheck;
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

		$nextPointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_ANON_CLASS, T_CLOSURE, T_FN, T_OPEN_SHORT_ARRAY], $controlStructurePointer + 1);
		if ($tokens[$nextPointer]['code'] === T_SEMICOLON) {
			return $nextPointer;
		}

		$nextPointer = $tokens[$nextPointer]['code'] === T_OPEN_SHORT_ARRAY
			? (int) TokenHelper::findNext($phpcsFile, T_SEMICOLON, $tokens[$nextPointer]['bracket_closer'] + 1)
			: (int) TokenHelper::findNext($phpcsFile, T_SEMICOLON, $tokens[$nextPointer]['scope_closer'] + 1);

		$level = $tokens[$controlStructurePointer]['level'];
		while ($level !== $tokens[$nextPointer]['level']) {
			$nextPointer = (int) TokenHelper::findNext($phpcsFile, T_SEMICOLON, $nextPointer + 1);
		}

		return $nextPointer;
	}

}
