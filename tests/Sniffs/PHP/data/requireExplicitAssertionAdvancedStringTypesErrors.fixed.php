<?php

$a = 'string';
\assert(\is_string($a) && $a !== '');

$a = 'string';
\assert(\is_string($a) && (bool) $a === true);

$b = 'MyClass::myCallbackMethod';
\assert(\is_string($b) && \is_callable($b));

$c = '100';
\assert(\is_string($c) && \is_numeric($c));

$d = null;
\assert($d instanceof SomeClass || $d === null);

/** @var class-string $e */
$e = Exception::class;
