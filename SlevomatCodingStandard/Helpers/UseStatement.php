<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class UseStatement
{

	public const TYPE_DEFAULT = ReferencedName::TYPE_DEFAULT;
	public const TYPE_FUNCTION = ReferencedName::TYPE_FUNCTION;
	public const TYPE_CONSTANT = ReferencedName::TYPE_CONSTANT;

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
		$this->normalizedNameAsReferencedInFile = self::normalizedNameAsReferencedInFile($type, $nameAsReferencedInFile);
		$this->fullyQualifiedTypeName = $fullyQualifiedClassName;
		$this->usePointer = $usePointer;
		$this->type = $type;
	}

	public static function normalizedNameAsReferencedInFile(string $type, string $name): string
	{
		if ($type !== self::TYPE_CONSTANT) {
			return strtolower($name);
		}

		$nameParts = explode(NamespaceHelper::NAMESPACE_SEPARATOR, $name);
		if (count($nameParts) === 1) {
			return $name;
		}

		return sprintf('%s%s%s', implode(NamespaceHelper::NAMESPACE_SEPARATOR, array_map(function (string $namePart): string {
			return strtolower($namePart);
		}, array_slice($nameParts, 0, -1))), NamespaceHelper::NAMESPACE_SEPARATOR, $nameParts[count($nameParts) - 1]);
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

		return $order[$this->type] <=> $order[$that->type];
	}

}
