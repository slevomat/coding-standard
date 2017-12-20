<?php

$bar === $foo;
123 === $foo;
true === $foo;
false === $foo;
null === $foo;
[] === $foo;
$this->foo() === $foo;
$foo === $this->foo();
[123] === [123];
array(123) === array(123);
array(123) === [123];
[123] === array(123);
123 === BAR;
123 === Foo::BAR;
123.0 === Foo::BAR;
\Foo\Bar\Baz::BAR === $e;
Foo::BAR === $foo;
Foo::BAR === $foo + 2;
Foo::BAR === $this->foo();
-1 === $foo;
+1 === $foo;
$neededReservationsAmount !== count($cartItem->getReservations());
strlen($part) - 1 !== $optionalPartOpeningBracePosition;
\Nette\Utils\Strings::length($part) - 1 !== $optionalPartOpeningBracePosition;
Foo::BAR === foo() + 2;

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

$bar === (int) $foo;
(int) $bar === $foo;
$foo === Foo::$bar;

switch (true) {
	case self::TIMEZONE_PHP_TYPE_OFFSET === $parsedTime['is_localtime'] && $parsedTime['zone_type']:
		$timezoneOffsetInMinutes = (-1) * $parsedTime['zone'];
		$timezoneOffsetHours = (int) floor($timezoneOffsetInMinutes / 60);
		$timezoneOffsetMinutes = $timezoneOffsetInMinutes % 60;
		$timezone = sprintf('%+02d:%02d', $timezoneOffsetHours, $timezoneOffsetMinutes);
		break;
	case self::TIMEZONE_PHP_TYPE_ABBREVIATION === $parsedTime['is_localtime'] && $parsedTime['zone_type']:
		$timezone = $parsedTime['tz_abbr'];
		break;
	case self::TIMEZONE_PHP_TYPE_IDENTIFIER === $parsedTime['is_localtime'] && $parsedTime['zone_type']:
		$timezone = $parsedTime['tz_id'];
		break;
	default:
		$timezone = 'UTC';
}

[self::ADMIN_EMAIL === $username ? self::ROLE_ADMIN : self::ROLE_CUSTOMER];
array(self::ADMIN_EMAIL === $username ? self::ROLE_ADMIN : self::ROLE_CUSTOMER);
[[] === $array];
[array() === $array];
$x = [$a, $b, $c] === $username;

A::TYPE_A === $param and A::TYPE_B === $param;
A::TYPE_A === $param or A::TYPE_B === $param;
A::TYPE_A === $param xor A::TYPE_B === $param;
