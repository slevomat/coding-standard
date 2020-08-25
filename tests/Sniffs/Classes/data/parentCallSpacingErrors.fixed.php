<?php

class X extends Whatever
{
	function a() {
		parent::a();

		echo 'wow';
	}

	function b() {
		echo 'wow';

		parent::b();
	}
}
