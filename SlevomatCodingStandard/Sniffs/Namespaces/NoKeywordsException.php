<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class NoKeywordsException extends \Exception
{

	/** @var string */
	private $sniffClassName;

	/** @var string */
	private $propertyName;

	/**
	 * @param string $sniffClassName
	 * @param string $propertyName
	 * @param \Exception|null $previous
	 */
	public function __construct($sniffClassName, $propertyName, \Exception $previous = null)
	{
		parent::__construct(sprintf(
			'Sniff %s requires an array of keywords set in property %s',
			$sniffClassName,
			$propertyName
		), 0, $previous);
		$this->sniffClassName = $sniffClassName;
		$this->propertyName = $propertyName;
	}

	/**
	 * @return string
	 */
	public function getSniffClassName()
	{
		return $this->sniffClassName;
	}

	/**
	 * @return string
	 */
	public function getPropertyName()
	{
		return $this->propertyName;
	}

}
