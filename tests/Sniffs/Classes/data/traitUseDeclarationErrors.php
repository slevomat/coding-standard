<?php

class Whatever
{

	use A, B;

	use C, D, E;

	use F, G {
		G::doSomething as doAnything;
	}

}
