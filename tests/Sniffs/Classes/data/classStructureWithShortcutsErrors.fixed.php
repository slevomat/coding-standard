<?php

abstract class Whatever
{
	public const PUBLIC_CONSTANT = 1;

	private const PRIVATE_CONSTANT = 0;
	private $privateProperty;

	public static $publicStaticProperty;

	private static $privateStaticProperty;

	public $nonStaticProperty;

	public function __construct()
	{
	}

	public function publicMethod()
	{
	}

	final protected function finalProtectedMethod()
	{
	}

	public abstract function abstractPublicMethod();

	abstract protected function abstractProtectedMethod();

	public static function publicStaticMethod()
	{
	}

	private static function privateStaticMethod()
	{
	}

	private function privateMethod()
	{
	}

	public function __destruct()
	{
	}

	protected function protectedMethod()
	{
	}

	public function __get($name)
	{
		return null;
	}

}
