<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHelper
{

	/**
	 * Validates type name according to the allowed characters in type names + namespaces
	 *
	 * @link http://php.net/manual/en/language.oop5.basic.php
	 *
	 * @param string $typeName
	 * @return bool
	 */
	public static function isTypeName(string $typeName): bool
	{
		$matches = [];
		$result = preg_match('~^(\\\?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+$~', $typeName, $matches);
		if ($result === false) {
			throw new \Exception('PREG error ' . preg_last_error());
		}

		return !($result === 0 || $matches === null);
	}

}
