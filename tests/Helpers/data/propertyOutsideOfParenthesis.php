<?php // lint >= 8.1

class OutsideOfParenthesis
{

	public int $typed;

	protected $untyped;

	private static $static;

	public static ?string $staticTyped;

	public readonly int $readonly;

	public $first, $second;

	public function __construct()
	{
		$local = 1;
	}

}
