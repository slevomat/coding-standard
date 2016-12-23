<?php

/**
 * @see https://www.slevomat.cz
 */
abstract class WithAnnotation
{

	/**
	 * @var bool
	 */
	const WITH_ANNOTATION = true;

	const WITHOUT_ANNOTATION = false;

	/**
	 * @var int
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
	 * @Route("/", name="homepage")
	 */
	public function withParametrizedAnnotation();

	/**
	 * Without annotation
	 */
	public function withoutAnnotation();

}
