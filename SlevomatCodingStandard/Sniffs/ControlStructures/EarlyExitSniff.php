<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class EarlyExitSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_EARLY_EXIT_NOT_USED = 'EarlyExitNotUsed';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_ELSE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $elsePointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $elsePointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$earlyExitTokenCodes = [T_RETURN, T_CONTINUE, T_BREAK, T_THROW, T_YIELD, T_EXIT];

		$lastSemicolonInElseScopePointer = TokenHelper::findPreviousEffective($phpcsFile, $tokens[$elsePointer]['scope_closer'] - 1);
		if ($lastSemicolonInElseScopePointer === null) {
			return;
		}

		$earlyExitInElseScopePointer = TokenHelper::findPreviousLocal($phpcsFile, $earlyExitTokenCodes, $lastSemicolonInElseScopePointer - 1, $tokens[$elsePointer]['scope_opener']);
		if ($earlyExitInElseScopePointer === null) {
			return;
		}

		$previousConditionCloseParenthesisPointer = TokenHelper::findPreviousEffective($phpcsFile, $elsePointer - 1);
		$previousConditionPointer = $tokens[$previousConditionCloseParenthesisPointer]['scope_condition'];

		if ($tokens[$previousConditionPointer]['code'] === T_ELSEIF) {
			return;
		}

		$ifPointer = $previousConditionPointer;

		$lastSemicolonInIfScopePointer = TokenHelper::findPreviousEffective($phpcsFile, $tokens[$ifPointer]['scope_closer'] - 1);
		if ($lastSemicolonInIfScopePointer === null) {
			return;
		}

		$earlyExitInIfScopePointer = TokenHelper::findPreviousLocal($phpcsFile, $earlyExitTokenCodes, $lastSemicolonInIfScopePointer - 1, $tokens[$ifPointer]['scope_opener']);
		if ($earlyExitInIfScopePointer !== null) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use early exit instead of else.',
			$elsePointer,
			self::CODE_EARLY_EXIT_NOT_USED
		);
		if ($fix) {
			$ifCondition = TokenHelper::getContent($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer'] - 1);
			$ifCode = TokenHelper::getContent($phpcsFile, $tokens[$ifPointer]['scope_opener'] + 1, $tokens[$ifPointer]['scope_closer'] - 1);
			$elseCode = TokenHelper::getContent($phpcsFile, $tokens[$elsePointer]['scope_opener'] + 1, $tokens[$elsePointer]['scope_closer'] - 1);

			$booleanPointer = TokenHelper::findNext($phpcsFile, \PHP_CodeSniffer\Util\Tokens::$booleanOperators, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer']);
			$booleanNotPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$ifPointer]['parenthesis_opener'] + 1);
			$comparisonPointer = TokenHelper::findNext($phpcsFile, [T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL, T_IS_GREATER_OR_EQUAL, T_LESS_THAN, T_GREATER_THAN], $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer']);

			if ($booleanPointer !== null) {
				$negativeIfCondition = sprintf('!(%s)', $ifCondition);
			} elseif ($tokens[$booleanNotPointer]['code'] === T_BOOLEAN_NOT) {
				$negativeIfCondition = ltrim($ifCondition, '!');
			} elseif (TokenHelper::findNext($phpcsFile, T_INSTANCEOF, $tokens[$ifPointer]['parenthesis_opener'] + 1, $tokens[$ifPointer]['parenthesis_closer']) !== null) {
				$negativeIfCondition = sprintf('!(%s)', $ifCondition);
			} elseif ($comparisonPointer !== null) {
				$negativeIfCondition = '';

				for ($i = $tokens[$ifPointer]['parenthesis_opener'] + 1; $i < $tokens[$ifPointer]['parenthesis_closer']; $i++) {
					$comparisonReplacements = [
						T_IS_EQUAL => '!=',
						T_IS_NOT_EQUAL => '==',
						T_IS_IDENTICAL => '!==',
						T_IS_NOT_IDENTICAL => '===',
						T_IS_GREATER_OR_EQUAL => '<',
						T_IS_SMALLER_OR_EQUAL => '>',
						T_GREATER_THAN => '<=',
						T_LESS_THAN => '>=',
					];

					$negativeIfCondition .= array_key_exists($tokens[$i]['code'], $comparisonReplacements) ? $comparisonReplacements[$tokens[$i]['code']] : $tokens[$i]['content'];
				}
			} else {
				$negativeIfCondition = sprintf('!%s', $ifCondition);
			}

			$phpcsFile->fixer->beginChangeset();

			for ($i = $ifPointer; $i <= $tokens[$elsePointer]['scope_closer']; $i++) {
				$phpcsFile->fixer->replaceToken($i, '');
			}

			$afterIfCode = implode($phpcsFile->eolChar, array_map(function (string $line): string {
				if ($line === '') {
					return $line;
				}

				if ($line[0] === "\t") {
					return substr($line, 1);
				}

				return substr($line, 4);
			}, explode($phpcsFile->eolChar, rtrim($ifCode))));

			$phpcsFile->fixer->addContent(
				$previousConditionPointer,
				sprintf(
					'if (%s) {%s}%s%s',
					$negativeIfCondition,
					$elseCode,
					$phpcsFile->eolChar,
					$afterIfCode
				)
			);

			$phpcsFile->fixer->endChangeset();
		}
	}

}
