<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\VariableHelper;
use const T_BOOLEAN_NOT;
use const T_CLOSURE;
use const T_DOLLAR;
use const T_EMPTY;
use const T_ISSET;
use const T_NS_SEPARATOR;
use const T_OPEN_PARENTHESIS;
use const T_PARENT;
use const T_SELF;
use const T_STATIC;
use const T_STRING;
use const T_UNSET;
use const T_USE;
use const T_VARIABLE;
use function array_key_exists;
use function in_array;

class UselessParenthesesSniff implements Sniff
{

	public const CODE_USELESS_PARENTHESES = 'UselessParentheses';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_PARENTHESIS,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $parenthesisOpenerPointer
	 */
	public function process(File $phpcsFile, $parenthesisOpenerPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (array_key_exists('parenthesis_owner', $tokens[$parenthesisOpenerPointer])) {
			return;
		}

		$pointerBeforeParenthesisOpener = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisOpenerPointer - 1);
		if (in_array($tokens[$pointerBeforeParenthesisOpener]['code'], [T_VARIABLE, T_STRING, T_ISSET, T_UNSET, T_EMPTY, T_CLOSURE, T_USE], true)) {
			return;
		}

		$this->checkParenthesesAroundVariableOrFunctionCall($phpcsFile, $parenthesisOpenerPointer);
	}

	private function checkParenthesesAroundVariableOrFunctionCall(File $phpcsFile, int $parenthesisOpenerPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		/** @var int $contentStartPointer */
		$contentStartPointer = TokenHelper::findNextEffective($phpcsFile, $parenthesisOpenerPointer + 1);
		$notBooleanNotOperatorPointer = $contentStartPointer;

		if ($tokens[$contentStartPointer]['code'] === T_BOOLEAN_NOT) {
			/** @var int $notBooleanNotOperatorPointer */
			$notBooleanNotOperatorPointer = TokenHelper::findNextEffective($phpcsFile, $contentStartPointer + 1);
		}

		if (in_array($tokens[$notBooleanNotOperatorPointer]['code'], [T_NS_SEPARATOR, T_STRING, T_SELF, T_STATIC, T_PARENT, T_VARIABLE, T_DOLLAR], true)) {
			$contentEndPointer = VariableHelper::findVariableEndPointer($phpcsFile, $notBooleanNotOperatorPointer);

			do {
				$nextPointer = TokenHelper::findNextEffective($phpcsFile, $contentEndPointer + 1);

				if ($tokens[$nextPointer]['code'] !== T_OPEN_PARENTHESIS) {
					break;
				}

				$contentEndPointer = $tokens[$nextPointer]['parenthesis_closer'];
			} while (true);
		} elseif (in_array($tokens[$notBooleanNotOperatorPointer]['code'], [T_ISSET, T_EMPTY], true)) {
			$nextPointer = TokenHelper::findNext($phpcsFile, T_OPEN_PARENTHESIS, $notBooleanNotOperatorPointer + 1);
			$contentEndPointer = $tokens[$nextPointer]['parenthesis_closer'];
		} else {
			return;
		}

		$pointerAfterContent = TokenHelper::findNextEffective($phpcsFile, $contentEndPointer + 1);

		if ($pointerAfterContent !== $tokens[$parenthesisOpenerPointer]['parenthesis_closer']) {
			return;
		}

		$fix = $phpcsFile->addFixableError('Useless parentheses.', $parenthesisOpenerPointer, self::CODE_USELESS_PARENTHESES);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		for ($i = $parenthesisOpenerPointer; $i < $contentStartPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		for ($i = $contentEndPointer + 1; $i <= $tokens[$parenthesisOpenerPointer]['parenthesis_closer']; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}
		$phpcsFile->fixer->endChangeset();
	}

}
