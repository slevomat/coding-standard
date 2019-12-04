<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ScopeHelper;
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

	/** @var bool */
	public $allowNested = true;

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_CLOSURE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $closurePointer
	 */
	public function process(File $phpcsFile, $closurePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$returnPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$closurePointer]['scope_opener'] + 1);
		if ($tokens[$returnPointer]['code'] !== T_RETURN) {
			return;
		}

		$usePointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$closurePointer]['parenthesis_closer'] + 1);
		if ($tokens[$usePointer]['code'] === T_USE) {
			$useOpenParenthesisPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);
			if (TokenHelper::findNext($phpcsFile, T_BITWISE_AND, $useOpenParenthesisPointer + 1, $tokens[$useOpenParenthesisPointer]['parenthesis_closer']) !== null) {
				return;
			}
		}

		if (!$this->allowNested) {
			$closureOrArrowFunctionPointer = TokenHelper::findNext($phpcsFile, [T_CLOSURE, T_FN], $tokens[$closurePointer]['scope_opener'] + 1, $tokens[$closurePointer]['scope_closer']);
			if ($closureOrArrowFunctionPointer !== null) {
				return;
			}
		}

		$fix = $phpcsFile->addFixableError(
			'Use arrow function.',
			$closurePointer,
			self::CODE_REQUIRED_ARROW_FUNCTION
		);
		if (!$fix) {
			return;
		}

		$pointerAfterReturn = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $returnPointer + 1);
		$semicolonAfterReturn = $this->findSemicolon($phpcsFile, $returnPointer);

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($closurePointer, 'fn');

		for ($i = $tokens[$closurePointer]['parenthesis_closer'] + 1; $i < $pointerAfterReturn; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->addContent($tokens[$closurePointer]['parenthesis_closer'], ' => ');

		for ($i = $semicolonAfterReturn; $i <= $tokens[$closurePointer]['scope_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

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
