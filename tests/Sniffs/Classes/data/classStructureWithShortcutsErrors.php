<?php

abstract class Whatever
{
	public static $publicStaticProperty;

	public const PUBLIC_CONSTANT = 1;
	private const PRIVATE_CONSTANT = 0;

	private static $privateStaticProperty;

	public $nonStaticProperty;

	private $privateProperty;

	public function __construct()
	{
	}

	public function __destruct()
	{
	}

	protected function protectedMethod()
	{
	}

	private function privateMethod()
	{
	}

	final protected function finalProtectedMethod()
	{
	}

	abstract protected function abstractProtectedMethod();

	public abstract function abstractPublicMethod();

	public static function publicStaticMethod()
	{
	}

	public function publicMethod()
	{
	}

	private static function privateStaticMethod()
	{
	}

	public function __get($name)
	{
		return null;
	}

}
