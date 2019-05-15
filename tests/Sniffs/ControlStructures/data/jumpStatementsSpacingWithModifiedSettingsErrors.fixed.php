<?php

function ($values) {
	foreach ($values as $value) {
		try {
			switch ($value) {
				case true:
					doSomething();
					break;



				case false:

					// With comment
					continue 2;



				case null:

					return;



				case 0:

					throw new Exception();



			}
		} catch (Throwable $e) {
		}
	}
};

label:




goto label;

function () {
	$array = [];
	yield from $array;



};

function () {
	if (true) {

		yield [];
		return;



	}
	yield [];



};
