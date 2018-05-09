<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Helpers\TokenHelper;

class DisallowIncrementAndDecrementOperatorsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_DISALLOWED_PRE_INCREMENT_OPERATOR = 'DisallowedPreIncrementOperator';
	public const CODE_DISALLOWED_POST_INCREMENT_OPERATOR = 'DisallowedPostIncrementOperator';
	public const CODE_DISALLOWED_PRE_DECREMENT_OPERATOR = 'DisallowedPreDecrementOperator';
	public const CODE_DISALLOWED_POST_DECREMENT_OPERATOR = 'DisallowedPostDecrementOperator';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_DEC,
			T_INC,
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

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $operatorPointer - 1);

		$isPostOperator = $tokens[$previousPointer]['code'] === T_VARIABLE;

		if ($tokens[$operatorPointer]['code'] === T_INC) {
			if ($isPostOperator) {
				$code = self::CODE_DISALLOWED_POST_INCREMENT_OPERATOR;
				$message = 'Use of post-increment operator is disallowed.';
			} else {
				$code = self::CODE_DISALLOWED_PRE_INCREMENT_OPERATOR;
				$message = 'Use of pre-increment operator is disallowed.';
			}
		} else {
			if ($isPostOperator) {
				$code = self::CODE_DISALLOWED_POST_DECREMENT_OPERATOR;
				$message = 'Use of post-decrement operator is disallowed.';
			} else {
				$code = self::CODE_DISALLOWED_PRE_DECREMENT_OPERATOR;
				$message = 'Use of pre-decrement operator is disallowed.';
			}
		}

		$phpcsFile->addError($message, $operatorPointer, $code);
	}

}
