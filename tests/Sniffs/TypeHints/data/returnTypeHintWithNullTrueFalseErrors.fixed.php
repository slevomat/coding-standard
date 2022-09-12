<?php // lint >= 8.2

class Whatever
{

	/***/
	public function returnsNull(): null
	{}

	/**
	 */
	public function returnsTrue(): true
	{}

	/**
	 */
	public function returnsFalse(): false
	{}

	/***/
	public function returnsNullWithUselessAnnotation(): null
	{}

	/**
	 */
	public function returnsTrueWithUselessAnnotation(): true
	{}

	/**
	 */
	public function returnsFalseWithUselessAnnotation(): false
	{}

}
