<?php

class X {
	function a() {
		parent::a();
		echo 'wow';
	}

	function b() {
		echo 'wow';
		parent::b();
	}
}
