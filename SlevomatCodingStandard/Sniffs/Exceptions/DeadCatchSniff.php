<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class DeadCatchSniff implements \PHP_CodeSniffer_Sniff
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
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $catchPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $catchPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$catchToken = $tokens[$catchPointer];
		$catchedTypeNameStartPointer = $phpcsFile->findNext(TokenHelper::$nameTokenCodes, $catchToken['parenthesis_opener'] + 1, $catchToken['parenthesis_closer']);
		$catchedVariablePointer = $phpcsFile->findNext(T_VARIABLE, $catchToken['parenthesis_opener'] + 1, $catchToken['parenthesis_closer']);
		$catchedTypeNameEndPointer = $phpcsFile->findPrevious(TokenHelper::$nameTokenCodes, $catchedVariablePointer - 1, $catchToken['parenthesis_opener']) + 1;
		$catchedType = NamespaceHelper::resolveName(
			$phpcsFile,
			TokenHelper::getContent($phpcsFile, $catchedTypeNameStartPointer, $catchedTypeNameEndPointer),
			UseStatementHelper::getUseStatements($phpcsFile, $phpcsFile->findNext(T_OPEN_TAG, 0)),
			$catchPointer
		);

		if ($catchedType !== '\\Throwable') {
			return;
		}

		$nextCatchPointer = TokenHelper::findNextEffective($phpcsFile, $catchToken['scope_closer'] + 1);

		while ($nextCatchPointer !== null) {
			$nextCatchToken = $tokens[$nextCatchPointer];
			if ($nextCatchToken['code'] !== T_CATCH) {
				break;
			}

			$phpcsFile->addError('Unreachable catch block', $nextCatchPointer, self::CODE_CATCH_AFTER_THROWABLE_CATCH);

			$nextCatchPointer = TokenHelper::findNextEffective($phpcsFile, $nextCatchToken['scope_closer'] + 1);
		}
	}

}
