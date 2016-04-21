<?php

/**
 * @see https://www.slevomat.cz
 */
abstract class WithAnnotation
{

	/**
	 * @var boolean
	 */
	const WITH_ANNOTATION = true;

	const WITHOUT_ANNOTATION = false;

	/**
	 * @var integer
	 */
	protected static $withAnnotation = 1;

	public $withoutAnnotation;

}

interface WithoutAnnotation
{

	/**
	 * @param string $a
	 * @param string $b
	 */
	public function withAnnotation($b, $c);

	/**
	 * Without annotation
	 */
	public function withoutAnnotation();

}
