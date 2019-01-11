<?php

/**
 * @see Anything
 */
class Whatever
{

	/**
	 * @var float|int
	 */
	private $noNull;

	/**
	 * @var
	 */
	private $invalidAnnotation;

	/**
	 * @var string
	 */
	private $noUnion;

	/** @var bool|null */
	private $varAnnotation;

	/**
	 * @param string|null $param
	 */
	public function paramAnnotation($param)
	{
		/** @var float|int|null $number */
		$inlineAnnotation = 0.0;

		/** @var $number float|int|null */
		$inlineAnnotationInSecondFormat = 0.0;
	}

	/**
	 * @return string|NULL
	 */
	public function returnAnnotation()
	{

	}

}

