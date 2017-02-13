<?php

$foo === $bar;
$foo === 123;
$foo === true;
$foo === false;
$foo === null;
$foo === [];
$foo === $this->foo();
$this->foo() === $foo;
[123] === [123];
BAR === 123;
Foo::BAR === 123;
Foo::BAR === 123.0;
$e === \Foo\Bar\Baz::BAR;
$foo === Foo::BAR;
$foo + 2 === Foo::BAR;
$this->foo() === Foo::BAR;
$foo === -1;
$foo === +1;
count($cartItem->getReservations()) !== $neededReservationsAmount;
$optionalPartOpeningBracePosition !== strlen($part) - 1;
$optionalPartOpeningBracePosition !== \Nette\Utils\Strings::length($part) - 1;
foo() + 2 === Foo::BAR;

if (
	$foo($bar) === [Foo::BAR, Foo::BAZ] && (
		$bar === true ||
		$bar === null
	)
) {
}

(int) $foo === $bar;
$foo === (int) $bar;
Foo::$bar === $foo;

switch (true) {
	case $parsedTime['is_localtime'] && $parsedTime['zone_type'] === self::TIMEZONE_PHP_TYPE_OFFSET:
		$timezoneOffsetInMinutes = (-1) * $parsedTime['zone'];
		$timezoneOffsetHours = (int) floor($timezoneOffsetInMinutes / 60);
		$timezoneOffsetMinutes = $timezoneOffsetInMinutes % 60;
		$timezone = sprintf('%+02d:%02d', $timezoneOffsetHours, $timezoneOffsetMinutes);
		break;
	case $parsedTime['is_localtime'] && $parsedTime['zone_type'] === self::TIMEZONE_PHP_TYPE_ABBREVIATION:
		$timezone = $parsedTime['tz_abbr'];
		break;
	case $parsedTime['is_localtime'] && $parsedTime['zone_type'] === self::TIMEZONE_PHP_TYPE_IDENTIFIER:
		$timezone = $parsedTime['tz_id'];
		break;
	default:
		$timezone = 'UTC';
}
