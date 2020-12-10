<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ScopeHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function array_merge;
use function count;
use function in_array;
use function rtrim;
use function sprintf;
use const T_CLOSE_PARENTHESIS;
use const T_COLON;
use const T_GOTO_LABEL;
use const T_ISSET;
use const T_OPEN_PARENTHESIS;
use const T_PARENT;
use const T_SELF;
use const T_STATIC;
use const T_UNSET;
use const T_VARIABLE;

class DisallowNamedArgumentsSniff implements Sniff
{

	public const CODE_DISALLOWED_NAMED_ARGUMENT = 'DisallowedNamedArgument';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_PARENTHESIS,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $parenthesisOpenerPointer
	 */
	public function process(File $phpcsFile, $parenthesisOpenerPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		if (array_key_exists('parenthesis_owner', $tokens[$parenthesisOpenerPointer])) {
			return;
		}

		$pointerBeforeParenthesisOpener = TokenHelper::findPreviousEffective($phpcsFile, $parenthesisOpenerPointer - 1);
		if (!in_array(
			$tokens[$pointerBeforeParenthesisOpener]['code'],
			array_merge(
				TokenHelper::getOnlyNameTokenCodes(),
				[T_VARIABLE, T_ISSET, T_UNSET, T_CLOSE_PARENTHESIS, T_SELF, T_STATIC, T_PARENT]
			),
			true
		)) {
			return;
		}

		$parenthesisCloserPointer = $tokens[$parenthesisOpenerPointer]['parenthesis_closer'];

		$colonPointers = TokenHelper::findNextAll(
			$phpcsFile,
			[T_GOTO_LABEL, T_COLON],
			$parenthesisOpenerPointer + 1,
			$parenthesisCloserPointer
		);

		if (count($colonPointers) === 0) {
			return;
		}

		foreach ($colonPointers as $colonPointer) {
			if ($tokens[$colonPointer]['code'] === T_COLON) {
				if (!ScopeHelper::isInSameScope($phpcsFile, $parenthesisOpenerPointer, $colonPointer)) {
					continue;
				}

				$argumentPointer = TokenHelper::findPreviousEffective($phpcsFile, $colonPointer - 1);

				if ($tokens[$argumentPointer]['code'] === T_CLOSE_PARENTHESIS) {
					continue;
				}

				$argumentName = $tokens[$argumentPointer]['content'];
			} else {
				$argumentPointer = $colonPointer;
				$argumentName = rtrim($tokens[$colonPointer]['content'], ':');
			}

			$phpcsFile->addError(
				sprintf('Named arguments are disallowed, usage of named argument "%s" found.', $argumentName),
				$argumentPointer,
				self::CODE_DISALLOWED_NAMED_ARGUMENT
			);
		}
	}

}
