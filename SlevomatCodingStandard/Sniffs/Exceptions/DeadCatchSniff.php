<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use SlevomatCodingStandard\Helpers\CatchHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class DeadCatchSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_CATCH_AFTER_THROWABLE_CATCH = 'CatchAfterThrowableCatch';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_CATCH,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $catchPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $catchPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$catchToken = $tokens[$catchPointer];
		$catchedTypes = CatchHelper::findCatchedTypesInCatch(
			$phpcsFile,
			UseStatementHelper::getUseStatements($phpcsFile, $phpcsFile->findNext(T_OPEN_TAG, 0)),
			$catchToken
		);

		if (!in_array('\\Throwable', $catchedTypes, true)) {
			return;
		}

		$nextCatchPointer = TokenHelper::findNextEffective($phpcsFile, $catchToken['scope_closer'] + 1);

		while ($nextCatchPointer !== null) {
			$nextCatchToken = $tokens[$nextCatchPointer];
			if ($nextCatchToken['code'] !== T_CATCH) {
				break;
			}

			$phpcsFile->addError('Unreachable catch block.', $nextCatchPointer, self::CODE_CATCH_AFTER_THROWABLE_CATCH);

			$nextCatchPointer = TokenHelper::findNextEffective($phpcsFile, $nextCatchToken['scope_closer'] + 1);
		}
	}

}
