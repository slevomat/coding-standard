<?php

class UnsortedSimple
{

	use C;
	use A;
	use B;

}

class UnsortedFqn
{

	use \Foo\C;
	use \Foo\A;
	use \Foo\B;

}

class UnsortedWithAdaptation
{

	use C {
		C::doSomething as doAnything;
	}
	use A;
	use B;

}

class UnsortedOneline
{

	use C, A, B;

}

class UnsortedAnother
{

	use B;
	use A;

}

class UnsortedMixed
{

	use B\C;
	use Abc;
	use \D\E\F;
	use A;

}

class UnsortedMixedOneline
{

	use B\C, \Abc, \D\E\F, A;

}
