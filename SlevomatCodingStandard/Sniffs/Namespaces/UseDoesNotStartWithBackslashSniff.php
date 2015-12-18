<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class UseDoesNotStartWithBackslashSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_STARTS_WITH_BACKSLASH = 'UseStartsWithBackslash';

	/**
	 * @return integer[]
	 */
	public function register()
	{
		return [
			T_USE,
		];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $usePointer
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $usePointer)
	{
		if (
			UseStatementHelper::isAnonymousFunctionUse($phpcsFile, $usePointer)
			|| UseStatementHelper::isTraitUse($phpcsFile, $usePointer)
		) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$nextTokenPointer = TokenHelper::findNextNonWhitespace($phpcsFile, $usePointer + 1);
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
