<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;

class TypeHelperTest extends TestCase
{

	/**
	 * @return list<array{0: string}>
	 */
	public static function dataValidTypeNames(): array
	{
		return [
			['Foo'],
			['\\Foo'],
			['Foo\\Bar'],
			['\\Foo\\Bar'],
			['Foo\\Bar\\Baz'],
			['\\Foo\\Bar\\Baz'],
			['Foo1'],
			['string'],
			['resource'],
			['self'],
			['static'],
		];
	}

	/**
	 * @dataProvider dataValidTypeNames
	 */
	#[DataProvider('dataValidTypeNames')]
	public function testValidTypeName(string $typeName): void
	{
		self::assertTrue(TypeHelper::isTypeName($typeName));
	}

	/**
	 * @return list<array{0: string}>
	 */
	public static function dataNotValidTypeNames(): array
	{
		return [
			['1Foo'],
			['$this'],
			['Foo\\'],
			['Foo[]'],
			['[]'],
			['\Dogma\Math\Range\RangeSet<T>'],
		];
	}

	/**
	 * @dataProvider dataNotValidTypeNames
	 */
	#[DataProvider('dataNotValidTypeNames')]
	public function testNotValidTypeName(string $typeName): void
	{
		self::assertFalse(TypeHelper::isTypeName($typeName));
	}

}
