<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class UndefinedKeywordTokenException extends \Exception
{

	/** @var string */
	private $keyword;

	/**
	 * @param string $keyword
	 * @param \Exception|null $previous
	 */
	public function __construct($keyword, \Exception $previous = null)
	{
		parent::__construct(sprintf(
			'Value for keyword token not found, constant "%s" is not defined',
			$keyword
		), 0, $previous);
		$this->keyword = $keyword;
	}

	/**
	 * @return string
	 */
	public function getKeyword()
	{
		return $this->keyword;
	}

}
