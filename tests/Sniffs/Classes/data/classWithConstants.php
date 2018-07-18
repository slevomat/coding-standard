<?php

class ClassWithConstants
{

	const PUBLIC_FOO = null;
	public const PUBLIC_BAR = null;

	protected const PROTECTED_FOO = null;
	private const PRIVATE_FOO = null;
	private const PRIVATE_BAR = null;

	public function __construct()
	{
		print_r(self::PRIVATE_BAR);
	}

}

$class = new class ()
{

	const PUBLIC_FOO = 'public';

};
