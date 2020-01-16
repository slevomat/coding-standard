<?php

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

}
