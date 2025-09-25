<?php

class ClassStructureSniffNoErrorsWithMethodGroupRulesData
{
	public function __construct()
	{
	}

	public function __destruct()
	{
	}

	public function inject($foo)
	{
	}

	public function injectFoo($foo)
	{
	}

	public static function setUpBeforeClass()
	{
	}

	#[PHPUnit\Framework\Attributes\BeforeClass]
	public static function beforeClassUsingAttribute()
	{
	}

	/**
	 * @beforeClass
	 */
	public static function beforeClassUsingAnnotation()
	{
	}

	protected function setUp()
	{
	}

	#[PHPUnit\Framework\Attributes\Before]
	protected function beforeUsingAttribute()
	{
	}

	/**
	 * @before
	 */
	protected function beforeUsingAnnotation()
	{
	}

	#[PHPUnit\Framework\Attributes\After]
	#[ReturnTypeWillChange]
	protected function afterUsingAttribute()
	{
	}

	/**
	 * @after
	 */
	#[ReturnTypeWillChange]
	protected function afterUsingAnnotation()
	{
	}

	public function lorem()
	{
	}

	protected function ipsum()
	{
	}

	private function dolor()
	{
	}

	public static function fooDataProvider(): \Generator
	{
		yield 'foo' => ['bar'];
	}
}
