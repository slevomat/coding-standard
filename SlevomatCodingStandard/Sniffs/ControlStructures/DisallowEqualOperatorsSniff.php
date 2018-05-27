<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class DisallowEqualOperatorsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_DISALLOWED_EQUAL_OPERATOR = 'DisallowedEqualOperator';
	public const CODE_DISALLOWED_NOT_EQUAL_OPERATOR = 'DisallowedNotEqualOperator';

	/**
	 * @return mixed[]
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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $operatorPointer): void
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
			$fix = $phpcsFile->addFixableError(sprintf(
				'Operator %s is disallowed, use !== instead.',
				$tokens[$operatorPointer]['content']
			), $operatorPointer, self::CODE_DISALLOWED_NOT_EQUAL_OPERATOR);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($operatorPointer, '!==');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}

}
