<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class MultipleUsesPerLineSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_MULTIPLE_USES_PER_LINE = 'MultipleUsesPerLine';

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
			return $usePointer + 1;
		}

		$endPointer = $phpcsFile->findNext(T_SEMICOLON, $usePointer + 1);
		$commaPointer = $phpcsFile->findNext(T_COMMA, $usePointer + 1, $endPointer);

		if ($commaPointer !== false) {
			$phpcsFile->addError(
				'Multiple used types per use statement are forbidden',
				$commaPointer,
				self::CODE_MULTIPLE_USES_PER_LINE
			);
		}
	}

}
