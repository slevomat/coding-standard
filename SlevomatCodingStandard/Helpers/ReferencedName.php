<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ReferencedName
{

	/** @var string */
	private $nameAsReferencedInFile;

	/** @var int */
	private $pointer;

	public function __construct(string $nameAsReferencedInFile, int $pointer)
	{
		$this->nameAsReferencedInFile = $nameAsReferencedInFile;
		$this->pointer = $pointer;
	}

	public function getNameAsReferencedInFile(): string
	{
		return $this->nameAsReferencedInFile;
	}

	public function getPointer(): int
	{
		return $this->pointer;
	}

}
