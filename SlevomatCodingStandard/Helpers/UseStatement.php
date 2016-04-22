<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class UseStatement
{

	/** @var string */
	private $nameAsReferencedInFile;

	/** @var string */
	private $normalizedNameAsReferencedInFile;

	/** @var string */
	private $fullyQualifiedTypeName;

	/** @var integer */
	private $usePointer;

	/**
	 * @param string $nameAsReferencedInFile
	 * @param string $fullyQualifiedClassName
	 * @param integer $usePointer T_USE pointer
	 */
	public function __construct(
		$nameAsReferencedInFile,
		$fullyQualifiedClassName,
		$usePointer
	)
	{
		$this->nameAsReferencedInFile = $nameAsReferencedInFile;
		$this->normalizedNameAsReferencedInFile = self::normalizedNameAsReferencedInFile($nameAsReferencedInFile);
		$this->fullyQualifiedTypeName = $fullyQualifiedClassName;
		$this->usePointer = $usePointer;
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

}
