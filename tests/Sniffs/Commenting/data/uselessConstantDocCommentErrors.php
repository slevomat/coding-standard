<?php

namespace Alphabet;

/**
 * @var string
 */
const NO_CLASS_CONSTANT = 'string';

class A
{

	/**
	 * AA constant
	 *
	 * @var string
	 */
	const AA = 'aa';

}

interface B
{

	/**
	 * BB constant
	 *
	 * @var bool
	 * @see anything
	 */
	public const BB = true;

}

new class implements B
{

	/**
	 *
	 * @var int
	 *
	 */
	const CC = 0;

};
