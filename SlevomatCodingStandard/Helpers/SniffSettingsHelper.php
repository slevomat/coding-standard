<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class SniffSettingsHelper
{

	/**
	 * @param mixed[] $settings
	 * @return mixed[]
	 */
	public static function normalizeArray(array $settings): array
	{
		$settings = array_map(function (string $value): string {
			return trim($value);
		}, $settings);
		$settings = array_filter($settings, function (string $value): bool {
			return $value !== '';
		});
		return array_values($settings);
	}

	/**
	 * @param mixed[] $settings
	 * @return mixed[]
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

}
