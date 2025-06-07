<?php

class ClassStructureSniffErrorsWithMethodGroupRulesData
{
	public function __construct()
	{
	}

	public function __destruct()
	{
	}

	#[PHPUnit\Framework\Attributes\Before]
	protected function beforeUsingAttribute()
	{
	}

	public function lorem()
	{
	}

	public static function setUpBeforeClass()
	{
	}

	#[PHPUnit\Framework\Attributes\BeforeClass]
	public static function beforeClassUsingAttribute()
	{
	}

	#[PHPUnit\Framework\Attributes\After]
	#[ReturnTypeWillChange]
	protected function afterUsingAttribute()
	{
	}

	protected function ipsum()
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

	/**
	 * @before
	 */
	protected function beforeUsingAnnotation()
	{
	}

	private function dolor()
	{
	}

	/**
	 * @after
	 */
	#[ReturnTypeWillChange]
	protected function afterUsingAnnotation()
	{
	}

	public function injectFoo($foo)
	{
	}

	public function inject($foo)
	{
	}
}
