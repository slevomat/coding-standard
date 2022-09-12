<?php // lint >= 8.2

class Whatever
{

	/** @return null */
	public function returnsNull()
	{}

	/**
	 * @return true
	 */
	public function returnsTrue()
	{}

	/**
	 * @return false
	 */
	public function returnsFalse()
	{}

	/** @return null */
	public function returnsNullWithUselessAnnotation(): null
	{}

	/**
	 * @return true
	 */
	public function returnsTrueWithUselessAnnotation(): true
	{}

	/**
	 * @return false
	 */
	public function returnsFalseWithUselessAnnotation(): false
	{}

}
