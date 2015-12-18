<?php

namespace SlevomatCodingStandard\Helpers;

class TokenPointerOutOfBoundsException extends \Exception
{

	/** @var integer */
	private $pointer;

	/** @var integer */
	private $lastTokenPointer;

	/**
	 * @param integer $pointer
	 * @param integer $lastTokenPointer
	 * @param \Exception|null $previous
	 */
	public function __construct($pointer, $lastTokenPointer, \Exception $previous = null)
	{
		parent::__construct(
			sprintf(
				'Attempted access to token pointer %d, last token pointer is %d',
				$pointer,
				$lastTokenPointer
			),
			0,
			$previous
		);

		$this->pointer = $pointer;
		$this->lastTokenPointer = $lastTokenPointer;
	}

	/**
	 * @return integer
	 */
	public function getPointer()
	{
		return $this->pointer;
	}

	/**
	 * @return integer
	 */
	public function getLastTokenPointer()
	{
		return $this->lastTokenPointer;
	}

}
