<?php // lint >= 8.0

function dummyFunctionWithPerlLikeComment()
{
	# PERL like comment
}

function dummyFunctionWithInlineComment()
{
	// Line comment
}

function dummyFunctionWithBlockComment()
{
	/*
	 Block comment
	 */
}

function dummyFunctionWithDocBlockComment()
{
	/**
	 * Doc block comment
	 */
}

function dummyFunctionWithEmptyComment()
{
	//
}

function dummyFunctionWithSomeBody()
{
	return 4;
}

abstract class DummyClass
{

	abstract public function dummyAbstractMethodWithoutBody();

	public function dummyMethodWithEmptyComment()
	{
		//
	}

	public function dummyMethodWithSomeBody()
	{
		return 4;
	}

	public function __construct(private $a, public $b)
	{
	}

}

class Foo
{

	private function __construct()
	{
	}

}
