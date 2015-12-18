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

}
