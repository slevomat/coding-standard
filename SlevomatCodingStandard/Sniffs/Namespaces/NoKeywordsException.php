<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class NoKeywordsException extends \Exception
{

	/** @var string */
	private $sniffClassName;

	/** @var string */
	private $propertyName;

	public function __construct(string $sniffClassName, string $propertyName, \Throwable $previous = null)
	{
		parent::__construct(sprintf(
			'Sniff %s requires an array of keywords set in property %s',
			$sniffClassName,
			$propertyName
		), 0, $previous);
		$this->sniffClassName = $sniffClassName;
		$this->propertyName = $propertyName;
	}

	public function getSniffClassName(): string
	{
		return $this->sniffClassName;
	}

	public function getPropertyName(): string
	{
		return $this->propertyName;
	}

}
