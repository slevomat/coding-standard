<?php

class NoUses
{

}

class SingleUse
{

	use A;

}

class AlreadySorted
{

	use A;
	use B;
	use C;

}

class AlreadySortedFqn
{

	use \Foo\A;
	use \Foo\B;
	use \Foo\C;

}

trait AlreadySortedTrait
{

	use A;
	use B;

}

class AlreadySortedWithAdaptation
{

	use A;
	use B {
		B::doSomething as doAnything;
	}
	use C;

}

class AlreadySortedMixed
{

	use A;
	use B\C;
	use \D\E\F;

}

class AlreadySortedMixedOneline
{

	use A, B\C, \D\E\F;

}
