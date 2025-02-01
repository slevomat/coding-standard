<?php

namespace Alphabet;

class A
{

	/**
	 * AA constant
	 *
	 */
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
