<?php

123 === $foo;
true === $foo;
false === $foo;
null === $foo;
[] === $foo;
123 === BAR;
123 === Foo::BAR;
123.0 === Foo::BAR;
\Foo\Bar::BAR === $e;
Foo::BAR === foo();
Foo::BAR === foo() + 2;
Foo::BAR === $foo;
Foo::BAR === $foo + 2;
Foo::BAR === $this->foo();
(BAR === foo() || (
	['test'] === Foo::BAR
)) ? 123.0 === Foo::BAR
	: null === $foo;
