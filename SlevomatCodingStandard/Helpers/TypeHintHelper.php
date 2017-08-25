<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHintHelper
{

	public static function isSimpleTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, self::getSimpleTypeHints(), true);
	}

	public static function isSimpleIterableTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, self::getSimpleIterableTypeHints(), true);
	}

	public static function convertLongSimpleTypeHintToShort(string $typeHint): string
	{
		$longToShort = [
			'integer' => 'int',
			'boolean' => 'bool',
		];
		return array_key_exists($typeHint, $longToShort) ? $longToShort[$typeHint] : $typeHint;
	}

	public static function getFullyQualifiedTypeHint(\PHP_CodeSniffer_File $phpcsFile, int $pointer, string $typeHint): string
	{
		if (self::isSimpleTypeHint($typeHint)) {
			return self::convertLongSimpleTypeHintToShort($typeHint);
		}

		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, TokenHelper::findPrevious($phpcsFile, T_OPEN_TAG, $pointer));
		return NamespaceHelper::resolveClassName($phpcsFile, $typeHint, $useStatements, $pointer);
	}

	/**
	 * @return string[]
	 */
	public static function getSimpleTypeHints(): array
	{
		return [
			'int',
			'integer',
			'float',
			'string',
			'bool',
			'boolean',
			'callable',
			'self',
			'array',
			'iterable',
			'void',
		];
	}

	/**
	 * @return string[]
	 */
	public static function getSimpleIterableTypeHints(): array
	{
		return [
			'array',
			'iterable',
		];
	}

	/**
	 * @return string[]
	 */
	public static function getSimpleUnofficialTypeHints(): array
	{
		return [
			'null',
			'mixed',
			'true',
			'false',
			'resource',
			'object',
			'static',
			'$this',
		];
	}

}
