<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHintHelper
{

	/** @var string[] */
	public static $simpleTypeHints = [
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

	/** @var string[] */
	public static $simpleIterableTypeHints = [
		'array',
		'iterable',
	];

	/** @var string[] */
	public static $simpleUnofficialTypeHints = [
		'null',
		'mixed',
		'true',
		'false',
		'resource',
		'object',
		'static',
		'$this',
	];

	public static function isSimpleTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, self::$simpleTypeHints, true);
	}

	public static function isSimpleIterableTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, self::$simpleIterableTypeHints, true);
	}

	public static function convertLongSimpleTypeHintToShort(string $typeHint): string
	{
		$longToShort = [
			'integer' => 'int',
			'boolean' => 'bool',
		];
		return array_key_exists($typeHint, $longToShort) ? $longToShort[$typeHint] : $typeHint;
	}

	public static function getFullyQualifiedTypeHint(\PHP_CodeSniffer\Files\File $phpcsFile, int $pointer, string $typeHint): string
	{
		if (self::isSimpleTypeHint($typeHint)) {
			return self::convertLongSimpleTypeHintToShort($typeHint);
		}

		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $phpcsFile->findPrevious(T_OPEN_TAG, $pointer));
		return NamespaceHelper::resolveName($phpcsFile, $typeHint, $useStatements, $pointer);
	}

}
