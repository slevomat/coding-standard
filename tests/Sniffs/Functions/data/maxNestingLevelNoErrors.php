<?php

abstract class DummyClass
{
	abstract function dummyFunction();
}

function dummyFunctionWithOneLevelNesting()
{
	$condition = function () {
		return 5;
	};

	if ($condition) {
		echo 'hi';
	}
}
