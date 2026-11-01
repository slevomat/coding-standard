<?php // lint >= 8.4

class Foo
{

	/** @var list<string> */
	private(set) array $privateSet;

	/** @var list<string> */
	protected(set) array $protectedSet;

	/** @var list<string> */
	public(set) array $publicSet;

	/** @var list<string> */
	public private(set) array $publicPrivateSet;

	/** @var list<string> */
	#[Assert\NotBlank]
	private(set) array $behindAttribute;

}
