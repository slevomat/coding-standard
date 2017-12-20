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

if (
	[Foo::BAR, Foo::BAZ] === $foo($bar) && (
		true === $bar ||
		null === $bar
	)
) {
}
if (
	array(Foo::BAR, Foo::BAZ) === $foo($bar) && (
		true === $bar ||
		null === $bar
	)
) {
}

$x = $a ?? 123 === $b;

switch ($c) {
	case 123 === $d:
		break;
}

$x === $$a;
FOO === (int) $bar;

$x = [self::ADMIN_EMAIL === $username ? self::ROLE_ADMIN : self::ROLE_CUSTOMER];
$x = [self::ADMIN_EMAIL === $username ? self::ROLE_ADMIN : self::ROLE_CUSTOMER];
$x = array(self::ADMIN_EMAIL === $username ? self::ROLE_ADMIN : self::ROLE_CUSTOMER);
$x = array(self::ADMIN_EMAIL === $username ? self::ROLE_ADMIN : self::ROLE_CUSTOMER);

$x = array(array() === $username ? true : false);
$x = array([] === $username ? true : false);
$x = [array() === $username ? true : false];
$x = [[] === $username ? true : false];

$x = [$a, $b, $c] === $username;

A::TYPE_A === $param and A::TYPE_B === $param;
A::TYPE_A === $param or A::TYPE_B === $param;
A::TYPE_A === $param xor A::TYPE_B === $param;
