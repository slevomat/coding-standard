<?php

namespace GetTokenExtension;

use PHP_CodeSniffer\Files\File;
use function PHPStan\Testing\assertType;
use const T_OPEN_SHORT_ARRAY;

class Foo
{

	public function doFoo(File $file): void
	{
		$tokens = $file->getTokens();
		foreach ($tokens as $token) {
			if ($token['code'] === T_ATTRIBUTE) {
				assertType('array{content: string, type: string, line: int, column: int, length: int, level: int, code: 351, attribute_opener: int, attribute_closer: int, conditions: array<int, int|string>, nested_parenthesis: array<int, int>}', $token);
			}
			if ($token['code'] === T_OPEN_SHORT_ARRAY) {
				assertType('array{content: string, type: string, line: int, column: int, length: int, level: int, code: \'PHPCS_T_OPEN_SHORT_ARRAY\', bracket_opener: int, bracket_closer: int, conditions: array<int, int|string>, nested_parenthesis: array<int, int>}', $token);
			}
		}
	}

}
