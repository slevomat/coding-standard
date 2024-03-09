<?php // lint >= 8.3

class ClassWithConstants
{

	const PUBLIC_FOO = null;
	public const PUBLIC_BAR = null;

	protected const PROTECTED_FOO = null;
	private const PRIVATE_FOO = null;
	private const PRIVATE_BAR = null;

	const int PUBLIC_INT_CONST = 1;

	public function __construct()
	{
		print_r(self::PRIVATE_BAR);
	}

}

$class = new class ()
{

	const PUBLIC_FOO = 'public';

	final const FINAL_WITHOUT_VISIBILITY = 'finalWithoutVisibility';
	public final const FINAL_WITH_VISIBILITY = 'finalWithVisibility';
	final public const FINAL_WITH_VISIBILITY2 = 'finalWithVisibility2';
};
