<?php

class Whatever
{

	use A;
	use B;

	use C;
	use D;
	use E;

	use F, G {
		G::doSomething as doAnything;
	}

}
