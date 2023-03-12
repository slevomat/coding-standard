<?php // lint >= 8.1

if (0) {
	function strlen(...$foo)
	{
	}
}

strlen($foo);
new Foo(...$foo);
(function (...$foo) {})(...$foo);

array_map(intval(...), ['123', '23']);
