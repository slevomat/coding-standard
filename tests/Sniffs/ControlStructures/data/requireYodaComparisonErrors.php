<?php

$foo === 123;
$foo === true;
$foo === false;
$foo === null;
$foo === [];
$foo === array();
BAR === 123;
Foo::BAR === 123;
Foo::BAR === 123.0;
$e === \Foo\Bar::BAR;
foo() === Foo::BAR;
foo() + 2 === Foo::BAR;
$foo === Foo::BAR;
$foo + 2 === Foo::BAR;
$this->foo() === Foo::BAR;
$foo === -1;
$foo === +1;
(foo() === BAR || (
	Foo::BAR === ['test']
)) ? Foo::BAR === 123.0
	: $foo === null;
(foo() === BAR || (
	Foo::BAR === array('test')
)) ? Foo::BAR === 123.0
	: $foo === null;
$bar === (int) FOO;
(int) $bar === FOO;
[$username === self::ADMIN_EMAIL ? self::ROLE_ADMIN : self::ROLE_CUSTOMER];
array($username === self::ADMIN_EMAIL ? self::ROLE_ADMIN : self::ROLE_CUSTOMER);
[$array === []];
[$array === array()];
$param === A::TYPE_A and $param === A::TYPE_B;
A::TYPE_A === $param or $param === A::TYPE_B;
$param === A::TYPE_A xor A::TYPE_B === $param;
$x = $username === [$a, $b, $c];
