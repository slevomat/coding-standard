<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class PropertyHelper
{

	public static function isProperty(\PHP_CodeSniffer_File $codeSnifferFile, int $variablePointer): bool
	{
		$propertyDeterminingPointer = TokenHelper::findPreviousExcluding(
			$codeSnifferFile,
			array_merge([T_STATIC], TokenHelper::$ineffectiveTokenCodes),
			$variablePointer - 1
		);

		return in_array($codeSnifferFile->getTokens()[$propertyDeterminingPointer]['code'], [
			T_PRIVATE,
			T_PROTECTED,
			T_PUBLIC,
			T_VAR,
		], true);
	}

}
