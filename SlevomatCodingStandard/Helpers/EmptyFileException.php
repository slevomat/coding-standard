<?php

namespace SlevomatCodingStandard\Helpers;

class EmptyFileException extends \Exception
{

	/** @var string */
	private $filename;

	/**
	 * @param string $filename
	 * @param \Exception|null $previous
	 */
	public function __construct($filename, \Exception $previous = null)
	{
		parent::__construct(sprintf(
			'File %s is empty',
			$filename
		), 0, $previous);

		$this->filename = $filename;
	}

	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

}
