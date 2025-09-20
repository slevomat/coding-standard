<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use const T_BITWISE_AND;
use const T_CLOSURE;
use const T_FN;
use const T_RETURN;
use const T_SEMICOLON;
use const T_USE;
use const T_WHITESPACE;

class RequireArrowFunctionSniff implements Sniff
{

	public const CODE_REQUIRED_ARROW_FUNCTION = 'RequiredArrowFunction';

	public bool $allowNested = true;

	public ?bool $enable = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CLOSURE,
		];
	}

	public function process(File $phpcsFile, int $closurePointer): void
	{
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 70400);

		if (!$this->enable) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$returnPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$closurePointer]['scope_opener'] + 1);
		if ($tokens[$returnPointer]['code'] !== T_RETURN) {
			return;
		}

		$usePointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$closurePointer]['parenthesis_closer'] + 1);
		if ($tokens[$usePointer]['code'] === T_USE && TokenHelper::findNext(
			$phpcsFile,
			T_BITWISE_AND,
			$tokens[$usePointer]['parenthesis_opener'] + 1,
			$tokens[$usePointer]['parenthesis_closer'],
		) !== null) {
			return;
		}

		if (!$this->allowNested) {
			$closureOrArrowFunctionPointer = TokenHelper::findNext(
				$phpcsFile,
				[T_CLOSURE, T_FN],
				$tokens[$closurePointer]['scope_opener'] + 1,
				$tokens[$closurePointer]['scope_closer'],
			);
			if ($closureOrArrowFunctionPointer !== null) {
				return;
			}
		}

		$fix = $phpcsFile->addFixableError('Use arrow function.', $closurePointer, self::CODE_REQUIRED_ARROW_FUNCTION);
		if (!$fix) {
			return;
		}

		$pointerAfterReturn = TokenHelper::findNextNonWhitespace($phpcsFile, $returnPointer + 1);
		$semicolonAfterReturn = $this->findSemicolon($phpcsFile, $returnPointer);
		$usePointer = TokenHelper::findNext(
			$phpcsFile,
			T_USE,
			$tokens[$closurePointer]['parenthesis_closer'] + 1,
			$tokens[$closurePointer]['scope_opener'],
		);
		$nonWhitespacePointerBeforeScopeOpener = TokenHelper::findPreviousExcluding(
			$phpcsFile,
			T_WHITESPACE,
			$tokens[$closurePointer]['scope_opener'] - 1,
		);

		$nonWhitespacePointerAfterUseParenthesisCloser = null;
		if ($usePointer !== null) {
			$nonWhitespacePointerAfterUseParenthesisCloser = TokenHelper::findNextExcluding(
				$phpcsFile,
				T_WHITESPACE,
				$tokens[$usePointer]['parenthesis_closer'] + 1,
			);
		}

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::replace($phpcsFile, $closurePointer, 'fn');

		if ($nonWhitespacePointerAfterUseParenthesisCloser !== null) {
			FixerHelper::removeBetween(
				$phpcsFile,
				$tokens[$closurePointer]['parenthesis_closer'],
				$nonWhitespacePointerAfterUseParenthesisCloser,
			);
		}

		FixerHelper::removeBetween($phpcsFile, $nonWhitespacePointerBeforeScopeOpener, $pointerAfterReturn);

		FixerHelper::add($phpcsFile, $nonWhitespacePointerBeforeScopeOpener, ' => ');

		FixerHelper::removeBetweenIncluding($phpcsFile, $semicolonAfterReturn, $tokens[$closurePointer]['scope_closer']);

		$phpcsFile->fixer->endChangeset();
	}

	private function findSemicolon(File $phpcsFile, int $pointer): int
	{
		$tokens = $phpcsFile->getTokens();

		$semicolonPointer = null;
		for ($i = $pointer + 1; $i < count($tokens) - 1; $i++) {
			if ($tokens[$i]['code'] !== T_SEMICOLON) {
				continue;
			}

			if (!ScopeHelper::isInSameScope($phpcsFile, $pointer, $i)) {
				continue;
			}

			$semicolonPointer = $i;
			break;
		}

		/** @var int $semicolonPointer */
		$semicolonPointer = $semicolonPointer;
		return $semicolonPointer;
	}

}
