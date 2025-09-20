<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function in_array;
use const T_CLOSE_CURLY_BRACKET;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_SEMICOLON;
use const T_WHITESPACE;

class DisallowEmptyFunctionSniff implements Sniff
{

	public const CODE_EMPTY_FUNCTION = 'EmptyFunction';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_FUNCTION];
	}

	public function process(File $phpcsFile, int $functionPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (FunctionHelper::isAbstract($phpcsFile, $functionPointer)) {
			return;
		}

		if (FunctionHelper::getName($phpcsFile, $functionPointer) === '__construct') {
			$previousPointer = TokenHelper::findPrevious(
				$phpcsFile,
				[T_PRIVATE, T_SEMICOLON, T_CLOSE_CURLY_BRACKET, T_OPEN_CURLY_BRACKET],
				$functionPointer - 1,
			);

			if ($previousPointer !== null && in_array($tokens[$previousPointer]['code'], [T_PRIVATE, T_PROTECTED], true)) {
				return;
			}

			$propertyPromotion = TokenHelper::findNext(
				$phpcsFile,
				Tokens::SCOPE_MODIFIERS,
				$tokens[$functionPointer]['parenthesis_opener'] + 1,
				$tokens[$functionPointer]['parenthesis_closer'],
			);

			if ($propertyPromotion !== null) {
				return;
			}
		}

		$firstContent = TokenHelper::findNextExcluding(
			$phpcsFile,
			T_WHITESPACE,
			$tokens[$functionPointer]['scope_opener'] + 1,
			$tokens[$functionPointer]['scope_closer'],
		);

		if ($firstContent !== null) {
			return;
		}

		$phpcsFile->addError(
			'Empty function body must have at least a comment to explain why is empty.',
			$functionPointer,
			self::CODE_EMPTY_FUNCTION,
		);
	}

}
