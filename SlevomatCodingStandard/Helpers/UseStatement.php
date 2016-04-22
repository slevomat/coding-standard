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

	/** @var int */
	private $usePointer;

	public function __construct(
		string $nameAsReferencedInFile,
		string $fullyQualifiedClassName,
		int $usePointer
	)
	{
		$this->nameAsReferencedInFile = $nameAsReferencedInFile;
		$this->normalizedNameAsReferencedInFile = self::normalizedNameAsReferencedInFile($nameAsReferencedInFile);
		$this->fullyQualifiedTypeName = $fullyQualifiedClassName;
		$this->usePointer = $usePointer;
	}

	public static function normalizedNameAsReferencedInFile(string $name): string
	{
		return strtolower($name);
	}

	public function getNameAsReferencedInFile(): string
	{
		return $this->nameAsReferencedInFile;
	}

	public function getCanonicalNameAsReferencedInFile(): string
	{
		return $this->normalizedNameAsReferencedInFile;
	}

	public function getFullyQualifiedTypeName(): string
	{
		return $this->fullyQualifiedTypeName;
	}

	public function getPointer(): int
	{
		return $this->usePointer;
	}

}
