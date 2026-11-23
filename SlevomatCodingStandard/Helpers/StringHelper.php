<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use function preg_match_all;
use function strlen;
use function strpos;
use function substr;

/**
 * @internal
 */
class StringHelper
{

	public static function startsWith(string $haystack, string $needle): bool
	{
		return $needle === '' || strpos($haystack, $needle) === 0;
	}

	public static function endsWith(string $haystack, string $needle): bool
	{
		return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
	}

	public static function charactersCount(string $string): int
	{
		$charactersCount = preg_match_all('/./us', $string);
		return $charactersCount === false ? strlen($string) : $charactersCount;
	}

}
