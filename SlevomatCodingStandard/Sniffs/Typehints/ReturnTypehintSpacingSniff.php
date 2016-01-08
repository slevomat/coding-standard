<?php

namespace SlevomatCodingStandard\Sniffs\Typehints;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\TokenHelper;

class ReturnTypehintSpacingSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE = 'NoSpaceBetweenColonAndType';

	const CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE = 'MultipleSpacesBetweenColonAndType';

	const CODE_WHITESPACE_BEFORE_COLON = 'WhitespaceBeforeColon';

	/**
	 * @return integer[]
	 */
	public function register()
	{
		return [
			T_RETURN_TYPE,
		];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $returnTypePointer
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $returnTypePointer)
	{
		$tokens = $phpcsFile->getTokens();
		$beforeNamePointer = TokenHelper::findPreviousExcluding($phpcsFile, [T_NS_SEPARATOR, T_STRING], $returnTypePointer - 1);
		$beforeNameToken = $tokens[$beforeNamePointer];

		$afterColonMessage = 'There must be exactly one space before return typehint.';
		if ($beforeNameToken['code'] === T_COLON) {
			$phpcsFile->addError($afterColonMessage, $returnTypePointer, self::CODE_NO_SPACE_BETWEEN_COLON_AND_TYPE);
			$colonPointer = $beforeNamePointer;
		} elseif ($beforeNameToken['code'] === T_WHITESPACE) {
			if ($beforeNameToken['content'] !== ' ') {
				$phpcsFile->addError($afterColonMessage, $returnTypePointer, self::CODE_MULTIPLE_SPACES_BETWEEN_COLON_AND_TYPE);
			}
			$colonPointer = $phpcsFile->findPrevious(T_COLON, $beforeNamePointer - 1);
		}

		$previousPointer = $colonPointer - 1;
		if ($tokens[$previousPointer]['code'] !== T_CLOSE_PARENTHESIS) {
			$phpcsFile->addError('There must be no whitespace between closing parenthesis and return type colon.', $returnTypePointer, self::CODE_WHITESPACE_BEFORE_COLON);
		}
	}

}
