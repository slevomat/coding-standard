<?php

namespace SlevomatCodingStandard\Helpers;

class SniffSettingsHelper
{

	/**
	 * @param mixed[] $settings
	 * @return mixed[]
	 */
	public static function normalizeArray(array $settings)
	{
		$settings = array_map(function ($value) {
			return trim($value);
		}, $settings);
		$settings = array_filter($settings, function ($value) {
			return $value !== '';
		});
		return array_values($settings);
	}

	/**
	 * @param mixed[] $settings
	 * @return mixed[]
	 */
	public static function normalizeAssociativeArray(array $settings)
	{
		$normalizedSettings = [];
		foreach ($settings as $key => $value) {
			$key = trim($key);
			$value = trim($value);
			if ($key === '' || $value === '') {
				continue;
			}
			$normalizedSettings[$key] = $value;
		}

		return $normalizedSettings;
	}

}
