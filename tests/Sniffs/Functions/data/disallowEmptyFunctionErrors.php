<?php

function dummyFunctionWithEmptyLines()
{



}

function dummyFunctionWithEmptyBody(){}

function dummyFunctionWithOneNewlineInBody()
{
}

function dummyFunctionWithEmptyComment()
{
	//
}

abstract class DummyClass
{

	abstract public function dummyAbstractMethodWithoutBody();

	public function dummyMethodWithNewlineInBody() {
	}

	public function dummyMethodWithNoContent() {}

}
