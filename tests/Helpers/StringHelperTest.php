<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;

class StringHelperTest extends TestCase
{

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	public static function dataStartsWith(): array
	{
		return [
			[
				'foo',
				'foo',
			],
			[
				'foobar',
				'foo',
			],
			[
				'foobar',
				'',
			],
		];
	}

	/**
	 * @dataProvider dataStartsWith
	 */
	#[DataProvider('dataStartsWith')]
	public function testStartsWith(string $haystack, string $needle): void
	{
		self::assertTrue(StringHelper::startsWith($haystack, $needle));
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	public static function dataNotStartsWith(): array
	{
		return [
			[
				'foo',
				'bar',
			],
			[
				'',
				'foo',
			],
			[
				'foobarfoo',
				'bar',
			],
			[
				'Foobar',
				'foo',
			],
		];
	}

	/**
	 * @dataProvider dataNotStartsWith
	 */
	#[DataProvider('dataNotStartsWith')]
	public function testNotStartsWith(string $haystack, string $needle): void
	{
		self::assertFalse(StringHelper::startsWith($haystack, $needle));
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	public static function dataEndsWith(): array
	{
		return [
			[
				'foobar',
				'bar',
			],
			[
				'foobar',
				'',
			],
			[
				'foobar',
				'foobar',
			],
		];
	}

	/**
	 * @dataProvider dataEndsWith
	 */
	#[DataProvider('dataEndsWith')]
	public function testEndsWith(string $haystack, string $needle): void
	{
		self::assertTrue(StringHelper::endsWith($haystack, $needle));
	}

	/**
	 * @return list<array{0: string, 1: string}>
	 */
	public static function dataNotEndsWith(): array
	{
		return [
			[
				'foobar',
				'foo',
			],
			[
				'',
				'foo',
			],
			[
				'barfoobar',
				'foo',
			],
			[
				'Foobar',
				'Bar',
			],
		];
	}

	/**
	 * @dataProvider dataNotEndsWith
	 */
	#[DataProvider('dataNotEndsWith')]
	public function testNotEndsWith(string $haystack, string $needle): void
	{
		self::assertFalse(StringHelper::endsWith($haystack, $needle));
	}

}
