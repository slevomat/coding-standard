<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHelperTest extends TestCase
{

	/**
	 * @return string[][]
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
	public function testValidTypeName(string $typeName): void
	{
		self::assertTrue(TypeHelper::isTypeName($typeName));
	}

	/**
	 * @return string[][]
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
	public function testNotValidTypeName(string $typeName): void
	{
		self::assertFalse(TypeHelper::isTypeName($typeName));
	}

}
