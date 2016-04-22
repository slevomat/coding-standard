<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ReferencedName
{

	/** @var string */
	private $nameAsReferencedInFile;

	/** @var integer */
	private $pointer;

	/**
	 * @param string $nameAsReferencedInFile
	 * @param integer $pointer
	 */
	public function __construct($nameAsReferencedInFile, $pointer)
	{
		$this->nameAsReferencedInFile = $nameAsReferencedInFile;
		$this->pointer = $pointer;
	}
	/**
	 * @return string
	 */
	public function getNameAsReferencedInFile()
	{
		return $this->nameAsReferencedInFile;
	}

	/**
	 * @return integer
	 */
	public function getPointer()
	{
		return $this->pointer;
	}

}
