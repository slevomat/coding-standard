<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\TokenHelper;

class ParameterTypeHintSpacingSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PARAMETER = 'NoSpaceBetweenTypeHintAndParameter';

	const CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER = 'MultipleSpacesBetweenTypeHintAndParameter';

	const CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL = 'WhitespaceAfterNullabilitySymbol';

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
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $functionPointer)
	{
		$tokens = $phpcsFile->getTokens();

		$parametersStartPointer = $tokens[$functionPointer]['parenthesis_opener'] + 1;
		$parametersEndPointer = $tokens[$functionPointer]['parenthesis_closer'] - 1;

		for ($i = $parametersStartPointer; $i <= $parametersEndPointer; $i++) {
			if ($tokens[$i]['code'] !== T_VARIABLE) {
				continue;
			}

			$parameterPointer = $i;
			$parameterName = $tokens[$parameterPointer]['content'];

			$parameterStartPointer = $phpcsFile->findPrevious(T_COMMA, $parameterPointer - 1, $parametersStartPointer);
			if ($parameterStartPointer === false) {
				$parameterStartPointer = $parametersStartPointer;
			}

			$parameterEndPointer = $phpcsFile->findNext(T_COMMA, $parameterPointer + 1, $parametersEndPointer + 1);
			if ($parameterEndPointer === false) {
				$parameterEndPointer = $parametersEndPointer;
			}

			$typeHintEndPointer = $phpcsFile->findPrevious(TokenHelper::$typeHintTokenCodes, $parameterPointer - 1, $parameterStartPointer);
			if ($typeHintEndPointer === false) {
				continue;
			}

			$nextTokenNames = [
				T_VARIABLE => sprintf('parameter %s', $parameterName),
				T_BITWISE_AND => sprintf('reference sign of parameter %s', $parameterName),
				T_ELLIPSIS => sprintf('varadic parameter %s', $parameterName),
			];
			$nextTokenPointer = $phpcsFile->findNext(array_keys($nextTokenNames), $typeHintEndPointer + 1, $parameterEndPointer + 1);

			if ($tokens[$typeHintEndPointer + 1]['code'] !== T_WHITESPACE) {
				$fix = $phpcsFile->addFixableError(sprintf('There must be exactly one space between parameter type hint and %s.', $nextTokenNames[$tokens[$nextTokenPointer]['code']]), $typeHintEndPointer, self::CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PARAMETER);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addContent($typeHintEndPointer, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			} elseif ($tokens[$typeHintEndPointer + 1]['content'] !== ' ') {
				$fix = $phpcsFile->addFixableError(sprintf('There must be exactly one space between parameter type hint and %s.', $nextTokenNames[$tokens[$nextTokenPointer]['code']]), $typeHintEndPointer, self::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PARAMETER);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($typeHintEndPointer + 1, ' ');
					$phpcsFile->fixer->endChangeset();
				}
			}

			$typeHintStartPointer = TokenHelper::findPreviousExcluding($phpcsFile, TokenHelper::$typeHintTokenCodes, $typeHintEndPointer, $parameterStartPointer) + 1;

			$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $typeHintStartPointer - 1, $parameterStartPointer);
			$nullabilitySymbolPointer = $previousPointer !== null && $tokens[$previousPointer]['code'] === T_NULLABLE ? $previousPointer : null;

			if ($nullabilitySymbolPointer === null) {
				continue;
			}

			if ($nullabilitySymbolPointer + 1 !== $typeHintStartPointer) {
				$fix = $phpcsFile->addFixableError(sprintf('There must be no whitespace between parameter type hint nullability symbol and parameter type hint of parameter %s.', $parameterName), $typeHintStartPointer, self::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($nullabilitySymbolPointer + 1, '');
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

}
