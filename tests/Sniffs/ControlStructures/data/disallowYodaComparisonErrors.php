<?php

123 === $foo;
true === $foo;
false === $foo;
null === $foo;
[] === $foo;
array() === $foo;
123 === BAR;
123 === Foo::BAR;
123.0 === Foo::BAR;
\Foo\Bar::BAR === $e;
Foo::BAR === foo();
Foo::BAR === foo() + 2;
Foo::BAR === $foo;
Foo::BAR === $foo + 2;
Foo::BAR === $this->foo();
-1 === $foo;
+1 === $foo;
(BAR === foo() || (
	['test'] === Foo::BAR
)) ? 123.0 === Foo::BAR
	: null === $foo;
(BAR === foo() || (
	array('test') === Foo::BAR
)) ? 123.0 === Foo::BAR
	: null === $foo;
(int) FOO === $bar;
FOO === (int) $bar;
[self::ADMIN_EMAIL === $username ? self::ROLE_ADMIN : self::ROLE_CUSTOMER];
array(self::ADMIN_EMAIL === $username ? self::ROLE_ADMIN : self::ROLE_CUSTOMER);
[[] === $array];
[array() === $array];
A::TYPE_A === $param and A::TYPE_B === $param;
$param === A::TYPE_A or A::TYPE_B === $param;
A::TYPE_A === $param xor $param === A::TYPE_B;
$x = [$a, $b, $c] === $username;
