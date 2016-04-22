<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class StringHelperTest extends \PHPUnit_Framework_TestCase
{

	public function dataStartsWith(): array
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
	 * @param string $haystack
	 * @param string $needle
	 */
	public function testStartsWith(string $haystack, string $needle)
	{
		$this->assertTrue(StringHelper::startsWith($haystack, $needle));
	}

	public function dataNotStartsWith(): array
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
	 * @param string $haystack
	 * @param string $needle
	 */
	public function testNotStartsWith(string $haystack, string $needle)
	{
		$this->assertFalse(StringHelper::startsWith($haystack, $needle));
	}

	public function dataEndsWith(): array
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
	 * @param string $haystack
	 * @param string $needle
	 */
	public function testEndsWith(string $haystack, string $needle)
	{
		$this->assertTrue(StringHelper::endsWith($haystack, $needle));
	}

	public function dataNotEndsWith(): array
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
	 * @param string $haystack
	 * @param string $needle
	 */
	public function testNotEndsWith(string $haystack, string $needle)
	{
		$this->assertFalse(StringHelper::endsWith($haystack, $needle));
	}

}
