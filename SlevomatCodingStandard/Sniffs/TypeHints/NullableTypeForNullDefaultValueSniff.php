<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\TokenHelper;

class NullableTypeForNullDefaultValueSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_NULLABILITY_SYMBOL_REQUIRED = 'NullabilitySymbolRequired';

	/**
	 * @return int[]
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
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $functionPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $functionPointer)
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

			$typeHintTokens = [T_NS_SEPARATOR, T_STRING, T_SELF, T_PARENT, T_ARRAY_HINT, T_CALLABLE];
			$ignoreTokensToFindTypeHint = array_merge(TokenHelper::$ineffectiveTokenCodes, [T_BITWISE_AND, T_ELLIPSIS]);
			$typeHintPointer = TokenHelper::findPreviousExcluding($phpcsFile, $ignoreTokensToFindTypeHint, $i - 1, $startPointer);

			if ($typeHintPointer === null || !in_array($tokens[$typeHintPointer]['code'], $typeHintTokens, true)) {
				continue;
			};

			$ignoreTokensToSkipTypeHint = array_merge(TokenHelper::$ineffectiveTokenCodes, $typeHintTokens);
			$beforeTypeHintPointer = TokenHelper::findPreviousExcluding($phpcsFile, $ignoreTokensToSkipTypeHint, $typeHintPointer - 1, $startPointer);

			if ($beforeTypeHintPointer !== null && $tokens[$beforeTypeHintPointer]['code'] === T_NULLABLE) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf('Parameter %s has null default value, but is not marked as nullable.', $parameterName),
				$i,
				self::CODE_NULLABILITY_SYMBOL_REQUIRED
			);

			if ($fix) {
				$firstTypehint = TokenHelper::findNextEffective($phpcsFile, $beforeTypeHintPointer === null ? $startPointer : $beforeTypeHintPointer + 1);

				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($firstTypehint - 1, '?');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
