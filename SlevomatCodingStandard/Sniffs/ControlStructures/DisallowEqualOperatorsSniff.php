<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class DisallowEqualOperatorsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_DISALLOWED_EQUAL_OPERATOR = 'DisallowedEqualOperator';
	const CODE_DISALLOWED_NOT_EQUAL_OPERATOR = 'DisallowedNotEqualOperator';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_IS_EQUAL,
			T_IS_NOT_EQUAL,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $operatorPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $operatorPointer)
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$operatorPointer]['code'] === T_IS_EQUAL) {
			$fix = $phpcsFile->addFixableError('Operator == is disallowed, use === instead.', $operatorPointer, self::CODE_DISALLOWED_EQUAL_OPERATOR);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($operatorPointer, '===');
				$phpcsFile->fixer->endChangeset();
			}
		} else {
			$fix = $phpcsFile->addFixableError('Operator != is disallowed, use !== instead.', $operatorPointer, self::CODE_DISALLOWED_NOT_EQUAL_OPERATOR);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($operatorPointer, '!==');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
