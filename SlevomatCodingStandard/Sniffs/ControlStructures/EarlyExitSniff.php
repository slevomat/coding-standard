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

		$pointerInElse = TokenHelper::findNextEffective($phpcsFile, $tokens[$elsePointer]['scope_opener'] + 1);

		$earlyExitTokenCodes = [T_RETURN, T_CONTINUE, T_BREAK, T_THROW, T_YIELD, T_EXIT];

		if (!in_array($tokens[$pointerInElse]['code'], $earlyExitTokenCodes, true)) {
			return;
		}

		$previousConditionCloseParenthesisPointer = TokenHelper::findPreviousEffective($phpcsFile, $elsePointer - 1);
		$previousConditionPointer = $tokens[$previousConditionCloseParenthesisPointer]['scope_condition'];

		if ($tokens[$previousConditionPointer]['code'] === T_ELSEIF) {
			return;
		}

		$pointerInIf = TokenHelper::findNextEffective($phpcsFile, $tokens[$previousConditionPointer]['scope_opener'] + 1);

		if (in_array($tokens[$pointerInIf]['code'], $earlyExitTokenCodes, true)) {
			return;
		}

		$phpcsFile->addError(
			'Use early exit instead of else.',
			$elsePointer,
			self::CODE_EARLY_EXIT_NOT_USED
		);
	}

}
