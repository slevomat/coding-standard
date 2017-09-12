<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TypeHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	/**
	 * @return string[][]
	 */
	public function dataValidTypeNames(): array
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
	 * @param string $typeName
	 */
	public function testValidTypeName(string $typeName): void
	{
		$this->assertTrue(TypeHelper::isTypeName($typeName));
	}

	/**
	 * @return string[][]
	 */
	public function dataNotValidTypeNames(): array
	{
		return [
			['1Foo'],
			['$this'],
			['Foo\\'],
			['Foo[]'],
			['[]'],
		];
	}

	/**
	 * @dataProvider dataNotValidTypeNames
	 * @param string $typeName
	 */
	public function testNotValidTypeName(string $typeName): void
	{
		$this->assertFalse(TypeHelper::isTypeName($typeName));
	}

}
