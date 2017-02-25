<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ReferencedName
{

	const TYPE_DEFAULT = 'default';
	const TYPE_FUNCTION = 'function';
	const TYPE_CONSTANT = 'constant';

	/** @var string */
	private $nameAsReferencedInFile;

	/** @var int */
	private $startPointer;

	/** @var int */
	private $endPointer;

	/** @var string */
	private $type;

	public function __construct(string $nameAsReferencedInFile, int $startPointer, int $endPointer, string $type)
	{
		$this->nameAsReferencedInFile = $nameAsReferencedInFile;
		$this->startPointer = $startPointer;
		$this->endPointer = $endPointer;
		$this->type = $type;
	}

	public function getNameAsReferencedInFile(): string
	{
		return $this->nameAsReferencedInFile;
	}

	public function getStartPointer(): int
	{
		return $this->startPointer;
	}

	public function getEndPointer(): int
	{
		return $this->endPointer;
	}

	public function isConstant(): bool
	{
		return $this->type === self::TYPE_CONSTANT;
	}

	public function isFunction(): bool
	{
		return $this->type === self::TYPE_FUNCTION;
	}

	public function hasSameUseStatementType(UseStatement $useStatement): bool
	{
		return $this->isConstant() === $useStatement->isConstant()
			&& $this->isFunction() === $useStatement->isFunction();
	}

}
