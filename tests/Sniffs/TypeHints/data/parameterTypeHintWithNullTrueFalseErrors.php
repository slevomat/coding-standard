<?php // lint >= 8.2

class Whatever
{

	/** @param null $a */
	public function parameterNull($a)
	{}

	/**
	 * @param true $a
	 */
	public function parameterTrue($a)
	{}

	/**
	 * @param false $a
	 */
	public function parameterFalse($a)
	{}

	/** @param null $a */
	public function parameterNullWithUselessAnnotation(null $a)
	{}

	/**
	 * @param true $a
	 */
	public function parameterTrueWithUselessAnnotation(true $a)
	{}

	/**
	 * @param false $a
	 */
	public function parameterFalseWithUselessAnnotation(false $a)
	{}

}
