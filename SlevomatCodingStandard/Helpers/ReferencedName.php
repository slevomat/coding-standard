<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ReferencedName
{

	const TYPE_DEFAULT = 'default';
	const TYPE_FUNCTION = 'function';
	const TYPE_CONSTANT = 'constant';

	/** @var string */
	private $nameAsReferencedInFile;

	/** @var integer */
	private $pointer;

	/** @var string */
	private $type;

	/**
	 * @param string $nameAsReferencedInFile
	 * @param integer $pointer
	 * @param string $type
	 */
	public function __construct($nameAsReferencedInFile, $pointer, $type)
	{
		$this->nameAsReferencedInFile = $nameAsReferencedInFile;
		$this->pointer = $pointer;
		$this->type = $type;
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

	/**
	 * @return boolean
	 */
	public function isConstant()
	{
		return $this->type === self::TYPE_CONSTANT;
	}

	/**
	 * @return boolean
	 */
	public function isFunction()
	{
		return $this->type === self::TYPE_FUNCTION;
	}

	/**
	 * @param \SlevomatCodingStandard\Helpers\UseStatement $useStatement
	 * @return boolean
	 */
	public function hasSameUseStatementType(UseStatement $useStatement)
	{
		return $this->isConstant() === $useStatement->isConstant()
			&& $this->isFunction() === $useStatement->isFunction();
	}

}
