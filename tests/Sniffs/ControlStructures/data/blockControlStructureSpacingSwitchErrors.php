<?php

switch ($condition)
{

	case 'a':

		return new A();


	case 'b':

		return new B();
	default:

		throw new InvalidArgumentException('...');

}
