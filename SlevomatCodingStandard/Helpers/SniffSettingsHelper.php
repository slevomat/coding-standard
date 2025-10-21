<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Config;
use function array_filter;
use function array_map;
use function array_values;
use function is_string;
use function preg_match;
use function trim;
use const PHP_VERSION_ID;

/**
 * @internal
 */
class SniffSettingsHelper
{

	/**
	 * @param string|int $settings
	 */
	public static function normalizeInteger($settings): int
	{
		return (int) trim((string) $settings);
	}

	/**
	 * @param string|int|null $settings
	 */
	public static function normalizeNullableInteger($settings): ?int
	{
		return $settings !== null ? (int) trim((string) $settings) : null;
	}

	/**
	 * @param list<string> $settings
	 * @return list<string>
	 */
	public static function normalizeArray(array $settings): array
	{
		$settings = array_map(static fn (string $value): string => trim($value), $settings);
		$settings = array_filter($settings, static fn (string $value): bool => $value !== '');
		return array_values($settings);
	}

	/**
	 * @param array<int|string, int|string> $settings
	 * @return array<int|string, int|string>
	 */
	public static function normalizeAssociativeArray(array $settings): array
	{
		$normalizedSettings = [];
		foreach ($settings as $key => $value) {
			if (is_string($key)) {
				$key = trim($key);
			}
			if (is_string($value)) {
				$value = trim($value);
			}
			if ($key === '' || $value === '') {
				continue;
			}
			$normalizedSettings[$key] = $value;
		}

		return $normalizedSettings;
	}

	public static function isValidRegularExpression(string $expression): bool
	{
		return preg_match('~^(?:\(.*\)|\{.*\}|\[.*\])[a-z]*\z~i', $expression) !== 0
			|| preg_match('~^([^a-z\s\\\\]).*\\1[a-z]*\z~i', $expression) !== 0;
	}

	public static function isEnabledByPhpVersion(?bool $value, int $phpVersionLimit): bool
	{
		if ($value !== null) {
			return $value;
		}

		$phpVersion = self::getPhpVersion() ?? PHP_VERSION_ID;
		return $phpVersion >= $phpVersionLimit;
	}

	public static function getPhpVersion(): ?int
	{
		$phpVersion = Config::getConfigData('php_version');

		return $phpVersion !== null ? (int) $phpVersion : null;
	}

}
