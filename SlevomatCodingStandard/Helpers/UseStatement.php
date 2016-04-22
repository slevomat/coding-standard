<?php declare(strict_types = 1);

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

	/** @var int */
	private $usePointer;

	/** @var string */
	private $type;

	public function __construct(
		string $nameAsReferencedInFile,
		string $fullyQualifiedClassName,
		int $usePointer,
		string $type
	)
	{
		$this->nameAsReferencedInFile = $nameAsReferencedInFile;
		$this->normalizedNameAsReferencedInFile = self::normalizedNameAsReferencedInFile($nameAsReferencedInFile);
		$this->fullyQualifiedTypeName = $fullyQualifiedClassName;
		$this->usePointer = $usePointer;
		$this->type = $type;
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

	public function isConstant(): bool
	{
		return $this->type === self::TYPE_CONSTANT;
	}

	public function isFunction(): bool
	{
		return $this->type === self::TYPE_FUNCTION;
	}

	public function hasSameType(self $that): bool
	{
		return $this->type === $that->type;
	}

	public function compareByType(self $that): int
	{
		$order = [
			self::TYPE_DEFAULT => 1,
			self::TYPE_CONSTANT => 2,
			self::TYPE_FUNCTION => 3,
		];

		return $order[$this->type] - $order[$that->type];
	}

}
