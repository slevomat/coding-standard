<?php

class Whatever
{

	public const ONE = 1;


	private const TWO = 2;


	/**
	 * @var int
	 */
	public $one = 1;
	/** @var string|null */
	private $two;
	/**
	 * @return object
	 */
	public function one()
	{
	}



	public function two()
	{

	} // Fucking comment


	use SomeTrait;
	use AnotherTrait;


	/** @return void */
	public function third()
	{

	} /* Fucking comment */


	private $third;

}
