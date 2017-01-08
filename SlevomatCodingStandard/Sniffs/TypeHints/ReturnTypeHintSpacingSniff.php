<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\TokenHelper;

class ReturnTypeHintSpacingSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE = 'NoSpaceBetweenColonAndType';

	const CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE = 'MultipleSpacesBetweenColonAndType';

	const CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL = 'NoSpaceBetweenColonAndNullabilitySymbol';

	const CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL = 'MultipleSpacesBetweenColonAndNullabilitySymbol';

	const CODE_WHITESPACE_BEFORE_COLON = 'WhitespaceBeforeColon';

	const CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL = 'WhitespaceAfterNullabilitySymbol';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_RETURN_TYPE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $typeHintPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $typeHintPointer)
	{
		$tokens = $phpcsFile->getTokens();

		$typeHintPointer = TokenHelper::findPreviousExcluding($phpcsFile, [T_NS_SEPARATOR, T_STRING], $typeHintPointer - 1) + 1;
		$colonPointer = TokenHelper::findPreviousExcluding($phpcsFile, array_merge([T_NULLABLE], TokenHelper::$ineffectiveTokenCodes), $typeHintPointer - 1);

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $colonPointer + 1);
		$nullabilitySymbolPointer = $nextPointer !== null && $tokens[$nextPointer]['code'] === T_NULLABLE ? $nextPointer : null;

		if ($nullabilitySymbolPointer === null) {
			if ($tokens[$colonPointer + 1]['code'] !== T_WHITESPACE) {
				$phpcsFile->addError('There must be exactly one space between return type hint colon and return type hint.', $typeHintPointer, self::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE);
			} elseif ($tokens[$colonPointer + 1]['content'] !== ' ') {
				$phpcsFile->addError('There must be exactly one space between return type hint colon and return type hint.', $typeHintPointer, self::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE);
			}
		} else {
			if ($tokens[$colonPointer + 1]['code'] !== T_WHITESPACE) {
				$phpcsFile->addError('There must be exactly one space between return type hint colon and return type hint nullability symbol.', $typeHintPointer, self::CODE_NO_SPACE_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
			} elseif ($tokens[$colonPointer + 1]['content'] !== ' ') {
				$phpcsFile->addError('There must be exactly one space between return type hint colon and return type hint nullability symbol.', $typeHintPointer, self::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_NULLABILITY_SYMBOL);
			}

			if ($nullabilitySymbolPointer + 1 !== $typeHintPointer) {
				$phpcsFile->addError('There must be no whitespace between return type hint nullability symbol and return type hint.', $typeHintPointer, self::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
			}
		}

		if ($tokens[$colonPointer - 1]['code'] !== T_CLOSE_PARENTHESIS) {
			$phpcsFile->addError('There must be no whitespace between closing parenthesis and return type colon.', $typeHintPointer, self::CODE_WHITESPACE_BEFORE_COLON);
		}
	}

}
