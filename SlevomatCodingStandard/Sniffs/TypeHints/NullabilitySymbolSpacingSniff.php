<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\TokenHelper;

class NullabilitySymbolSpacingSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT = 'WhitespaceBetweenNullabilitySymbolAndTypeHint';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_NULLABLE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $nullabilitySymbolPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $nullabilitySymbolPointer)
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$nullabilitySymbolPointer + 1]['code'] === T_WHITESPACE) {
			$typeHintStartPointer = TokenHelper::findNextExcluding($phpcsFile, T_WHITESPACE, $nullabilitySymbolPointer + 1);
			$typeHintEndPointer = TokenHelper::findNextExcluding($phpcsFile, [T_STRING, T_NS_SEPARATOR, T_ARRAY_HINT, T_CALLABLE, T_SELF, T_PARENT, T_RETURN_TYPE], $typeHintStartPointer + 1) - 1;

			$fix = $phpcsFile->addFixableError(
				sprintf('There must be no whitespace between nullability symbol and type hint "%s".', TokenHelper::getContent($phpcsFile, $typeHintStartPointer, $typeHintEndPointer)),
				$nullabilitySymbolPointer,
				self::CODE_WHITESPACE_BETWEEN_NULLABILITY_SYMBOL_AND_TYPE_HINT
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($nullabilitySymbolPointer + 1, '');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
