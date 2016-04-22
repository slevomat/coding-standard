<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class StringHelper
{

	public static function startsWith(string $haystack, string $needle): bool
	{
		return strncmp($haystack, $needle, strlen($needle)) === 0;
	}

	public static function endsWith(string $haystack, string $needle): bool
	{
		return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
	}

}
