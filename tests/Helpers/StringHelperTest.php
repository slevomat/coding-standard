<?php

namespace SlevomatCodingStandard\Helpers;

class StringHelperTest extends \PHPUnit_Framework_TestCase
{

	public function dataStartsWith()
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
	public function testStartsWith($haystack, $needle)
	{
		$this->assertTrue(StringHelper::startsWith($haystack, $needle));
	}

	public function dataNotStartsWith()
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
	public function testNotStartsWith($haystack, $needle)
	{
		$this->assertFalse(StringHelper::startsWith($haystack, $needle));
	}

	public function dataEndsWith()
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
	public function testEndsWith($haystack, $needle)
	{
		$this->assertTrue(StringHelper::endsWith($haystack, $needle));
	}

	public function dataNotEndsWith()
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
	public function testNotEndsWith($haystack, $needle)
	{
		$this->assertFalse(StringHelper::endsWith($haystack, $needle));
	}

}
