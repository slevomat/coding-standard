<?php // lint >= 8.2

class Whatever
{

	/** */
	public function parameterNull(null $a)
	{}

	/**
	 */
	public function parameterTrue(true $a)
	{}

	/**
	 */
	public function parameterFalse(false $a)
	{}

	/** */
	public function parameterNullWithUselessAnnotation(null $a)
	{}

	/**
	 */
	public function parameterTrueWithUselessAnnotation(true $a)
	{}

	/**
	 */
	public function parameterFalseWithUselessAnnotation(false $a)
	{}

}
