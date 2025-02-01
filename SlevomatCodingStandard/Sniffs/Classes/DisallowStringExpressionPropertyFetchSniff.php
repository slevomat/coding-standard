<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function preg_match;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_OBJECT_OPERATOR;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_PARENTHESIS;

class DisallowStringExpressionPropertyFetchSniff implements Sniff
{

	public const CODE_DISALLOWED_STRING_EXPRESSION_PROPERTY_FETCH = 'DisallowedStringExpressionPropertyFetch';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_OBJECT_OPERATOR];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $objectOperatorPointer
	 */
	public function process(File $phpcsFile, $objectOperatorPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$curlyBracketOpenerPointer = TokenHelper::findNextEffective($phpcsFile, $objectOperatorPointer + 1);

		if ($tokens[$curlyBracketOpenerPointer]['code'] !== T_OPEN_CURLY_BRACKET) {
			return;
		}

		$curlyBracketCloserPointer = $tokens[$curlyBracketOpenerPointer]['bracket_closer'];

		if (TokenHelper::findNextExcluding(
			$phpcsFile,
			T_CONSTANT_ENCAPSED_STRING,
			$curlyBracketOpenerPointer + 1,
			$curlyBracketCloserPointer,
		) !== null) {
			return;
		}

		$pointerAfterCurlyBracketCloser = TokenHelper::findNextEffective($phpcsFile, $curlyBracketCloserPointer + 1);

		if ($tokens[$pointerAfterCurlyBracketCloser]['code'] === T_OPEN_PARENTHESIS) {
			return;
		}

		if (preg_match(
			'~^(["\'])([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\1$~',
			$tokens[$curlyBracketOpenerPointer + 1]['content'],
			$matches,
		) !== 1) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'String expression property fetch is disallowed, use identifier property fetch.',
			$curlyBracketOpenerPointer,
			self::CODE_DISALLOWED_STRING_EXPRESSION_PROPERTY_FETCH,
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change($phpcsFile, $curlyBracketOpenerPointer, $curlyBracketCloserPointer, $matches[2]);

		$phpcsFile->fixer->endChangeset();
	}

}
