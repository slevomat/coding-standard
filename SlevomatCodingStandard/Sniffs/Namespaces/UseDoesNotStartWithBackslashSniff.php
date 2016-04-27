<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class UseDoesNotStartWithBackslashSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_STARTS_WITH_BACKSLASH = 'UseStartsWithBackslash';

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_USE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $usePointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $usePointer)
	{
		if (
			UseStatementHelper::isAnonymousFunctionUse($phpcsFile, $usePointer)
			|| UseStatementHelper::isTraitUse($phpcsFile, $usePointer)
		) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $usePointer + 1);
		$nextToken = $tokens[$nextTokenPointer];

		if ($nextToken['code'] === T_NS_SEPARATOR) {
			$phpcsFile->addError(
				'Use statement cannot start with a backslash',
				$nextTokenPointer,
				self::CODE_STARTS_WITH_BACKSLASH
			);
		}
	}

}
