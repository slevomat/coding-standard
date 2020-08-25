<?php

class Whatever
{

	// Comment
	use A;
	use B {
		B::b as bb;
	}
	// Comment
	use C;
	use D;

	public function __construct()
	{

	}

}
