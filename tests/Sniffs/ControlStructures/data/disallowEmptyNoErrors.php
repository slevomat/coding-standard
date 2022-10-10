<?php

$boo = isset($foo) ? false : true;

if (!$foo) {

}

class X {
    public static function empty(): self
    {
		return $this;
	}
}
