<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class ReturnTypeHint
{

	/** @var string */
	private $typeHint;

	/** @var bool */
	private $nullable;

	public function __construct(
		string $typeHint,
		bool $nullable
	)
	{
		$this->typeHint = $typeHint;
		$this->nullable = $nullable;
	}

	public function getTypeHint(): string
	{
		return $this->typeHint;
	}

	public function isNullable(): bool
	{
		return $this->nullable;
	}

}
