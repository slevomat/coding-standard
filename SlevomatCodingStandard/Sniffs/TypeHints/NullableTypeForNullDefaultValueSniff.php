<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_merge;
use function in_array;
use function sprintf;
use const T_BITWISE_AND;
use const T_CLOSURE;
use const T_ELLIPSIS;
use const T_EQUAL;
use const T_FUNCTION;
use const T_NULL;
use const T_NULLABLE;
use const T_VARIABLE;

class NullableTypeForNullDefaultValueSniff implements Sniff
{

	public const CODE_NULLABILITY_SYMBOL_REQUIRED = 'NullabilitySymbolRequired';

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_CLOSURE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 */
	public function process(File $phpcsFile, $functionPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$startPointer = $tokens[$functionPointer]['parenthesis_opener'] + 1;
		$endPointer = $tokens[$functionPointer]['parenthesis_closer'];

		for ($i = $startPointer; $i < $endPointer; $i++) {
			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			$parameterName = $tokens[$i]['content'];

			$afterVariablePointer = TokenHelper::findNextEffective($phpcsFile, $i + 1);
			if ($tokens[$afterVariablePointer]['code'] !== T_EQUAL) {
				continue;
			}

			$afterEqualsPointer = TokenHelper::findNextEffective($phpcsFile, $afterVariablePointer + 1);
			if ($tokens[$afterEqualsPointer]['code'] !== T_NULL) {
				continue;
			}

			$ignoreTokensToFindTypeHint = array_merge(TokenHelper::$ineffectiveTokenCodes, [T_BITWISE_AND, T_ELLIPSIS]);
			$typeHintPointer = TokenHelper::findPreviousExcluding($phpcsFile, $ignoreTokensToFindTypeHint, $i - 1, $startPointer);

			if ($typeHintPointer === null || !in_array($tokens[$typeHintPointer]['code'], TokenHelper::$typeHintTokenCodes, true)) {
				continue;
			}

			$ignoreTokensToSkipTypeHint = array_merge(TokenHelper::$ineffectiveTokenCodes, TokenHelper::$typeHintTokenCodes);
			$beforeTypeHintPointer = TokenHelper::findPreviousExcluding($phpcsFile, $ignoreTokensToSkipTypeHint, $typeHintPointer - 1, $startPointer);

			if ($beforeTypeHintPointer !== null && $tokens[$beforeTypeHintPointer]['code'] === T_NULLABLE) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf('Parameter %s has null default value, but is not marked as nullable.', $parameterName),
				$i,
				self::CODE_NULLABILITY_SYMBOL_REQUIRED
			);

			if (!$fix) {
				continue;
			}

			$firstTypehint = TokenHelper::findNextEffective($phpcsFile, $beforeTypeHintPointer === null ? $startPointer : $beforeTypeHintPointer + 1);

			$phpcsFile->fixer->beginChangeset();
			$phpcsFile->fixer->addContent($firstTypehint - 1, '?');
			$phpcsFile->fixer->endChangeset();
		}
	}

}
