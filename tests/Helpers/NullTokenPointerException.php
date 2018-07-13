<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use Exception;
use Throwable;

class NullTokenPointerException extends Exception
{

	public function __construct(?Throwable $previous = null)
	{
		parent::__construct('', 0, $previous);
	}

}
