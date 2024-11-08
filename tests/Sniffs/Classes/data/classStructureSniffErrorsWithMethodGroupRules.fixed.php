<?php

class ClassStructureSniffErrorsWithMethodGroupRulesData
{
	public function __construct()
	{
	}

	public function __destruct()
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

	#[PHPUnit\Framework\Attributes\Before]
	protected function beforeUsingAttribute()
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
}
