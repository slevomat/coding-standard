<?php

namespace Alphabet;

const NO_CLASS_CONSTANT = 'string';

class A
{

	const AA = 'aa';

}

interface B
{

	/**
	 * BB constant
	 *
	 * @see anything
	 */
	public const BB = true;

}

new class implements B
{

	const CC = 0;

};
