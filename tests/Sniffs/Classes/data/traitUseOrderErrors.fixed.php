<?php

class UnsortedSimple
{

	use A;
	use B;
	use C;

}

class UnsortedFqn
{

	use \Foo\A;
	use \Foo\B;
	use \Foo\C;

}

class UnsortedWithAdaptation
{

	use A;
	use B;
	use C {
		C::doSomething as doAnything;
	}

}

class UnsortedOneline
{

	use A, B, C;

}

class UnsortedAnother
{

	use A;
	use B;

}

class UnsortedMixed
{

	use A;
	use Abc;
	use B\C;
	use \D\E\F;

}

class UnsortedMixedOneline
{

	use A, \Abc, B\C, \D\E\F;

}
