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

class UnsortedWithDocBlocks
{

	/** @use AnotherGenericTrait<int> */
	use AnotherGenericTrait;
	/** @use GenericTrait<string> */
	use GenericTrait;

}

class UnsortedWithInlineComments
{

	// AnotherGenericTrait comment
	use AnotherGenericTrait;
	// GenericTrait comment
	use GenericTrait;

}

class UnsortedWithMultiLineInlineComments
{

	// AnotherGenericTrait comment
	// second line
	use AnotherGenericTrait;
	// GenericTrait comment
	// second line
	use GenericTrait;

}

class UnsortedWithMultiLineDocBlocks
{

	/**
	 * @use AnotherGenericTrait<int>
	 */
	use AnotherGenericTrait;
	/**
	 * @use GenericTrait<string>
	 */
	use GenericTrait;

}
