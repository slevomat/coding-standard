<?php

class Whatever
{
	use A;

	use B {
		B::b as bb;
	}

	use C;

	use D;


	public function __construct()
	{

	}

}
