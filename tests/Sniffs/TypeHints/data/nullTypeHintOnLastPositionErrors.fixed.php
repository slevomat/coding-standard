<?php

class Whatever
{

	/**
	 * @var bool|null
	 */
	private $multilineAnnotation;

	/** @var bool|null */
	private $singleLineAnnotation;

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
	 * @return string|null
	 */
	public function returnAnnotation()
	{

	}

	/**
	 * @return string|NULL
	 */
	public function uppercasedNull()
	{

	}

	/**
	 * @return float|int|null
	 */
	public function nullInTheMiddle()
	{

	}

}

