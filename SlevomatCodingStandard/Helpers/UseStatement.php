<?php

namespace SlevomatCodingStandard\Helpers;

class UseStatement
{

	const TYPE_DEFAULT = ReferencedName::TYPE_DEFAULT;
	const TYPE_FUNCTION = ReferencedName::TYPE_FUNCTION;
	const TYPE_CONSTANT = ReferencedName::TYPE_CONSTANT;

	/** @var string */
	private $nameAsReferencedInFile;

	/** @var string */
	private $normalizedNameAsReferencedInFile;

	/** @var string */
	private $fullyQualifiedTypeName;

	/** @var integer */
	private $usePointer;

	/** @var string */
	private $type;

	/**
	 * @param string $nameAsReferencedInFile
	 * @param string $fullyQualifiedClassName
	 * @param integer $usePointer T_USE pointer
	 * @param string $type
	 */
	public function __construct(
		$nameAsReferencedInFile,
		$fullyQualifiedClassName,
		$usePointer,
		$type
	)
	{
		$this->nameAsReferencedInFile = $nameAsReferencedInFile;
		$this->normalizedNameAsReferencedInFile = self::normalizedNameAsReferencedInFile($nameAsReferencedInFile);
		$this->fullyQualifiedTypeName = $fullyQualifiedClassName;
		$this->usePointer = $usePointer;
		$this->type = $type;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public static function normalizedNameAsReferencedInFile($name)
	{
		return strtolower($name);
	}

	/**
	 * @return string
	 */
	public function getNameAsReferencedInFile()
	{
		return $this->nameAsReferencedInFile;
	}

	/**
	 * @return string
	 */
	public function getCanonicalNameAsReferencedInFile()
	{
		return $this->normalizedNameAsReferencedInFile;
	}

	/**
	 * @return string
	 */
	public function getFullyQualifiedTypeName()
	{
		return $this->fullyQualifiedTypeName;
	}

	/**
	 * @return integer
	 */
	public function getPointer()
	{
		return $this->usePointer;
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
	 * @param self $that
	 * @return boolean
	 */
	public function hasSameType(self $that)
	{
		return $this->type === $that->type;
	}

	/**
	 * @param self $that
	 * @return integer
	 */
	public function compareByType(self $that)
	{
		$order = [
			self::TYPE_DEFAULT => 1,
			self::TYPE_CONSTANT => 2,
			self::TYPE_FUNCTION => 3,
		];

		return $order[$this->type] - $order[$that->type];
	}

}
