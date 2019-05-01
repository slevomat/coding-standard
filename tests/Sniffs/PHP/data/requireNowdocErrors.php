<?php

$abc = 'abc';

$a = <<<HED
	\$abc
HED;

$b = <<<"HED"
	\$abc
HED;

$c = <<<"HED"
	{\$abc}
	\\n\\r\\t\\v\\e\\f
	\\
	\$
	\\400
	\\x0a
	\\u{0a}
HED;
