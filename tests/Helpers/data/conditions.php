<?php

if ($foo) {

}

if ($foo->isValid()) {

}

if ($foo === null) {

}

if ($foo->getSomething() > 0) {

}

if ($foo || $boo) {

}

if (max(0, 10)) {

}

if ((true) || (false)) {

}

if (true) {

}

if (false) {

}

if ($flag && [0 => $a === 1, 1 => 'fallback']) {

}

if ([0 => $a === 1, 1 => 'fallback']) {

}

if ([0 => $a] === [0 => $b]) {

}

if ($a === [0 => $b === 1]) {

}

if ([0 => $a && $b]) {

}

if ((new class () { public function foo(): int { return 1; } }) !== null) {

}

if ($a === 1) {

}

if (!$a) {

}

if (!($a && $b)) {

}

if ($a && !($b && $c)) {

}

if ($a instanceof Foo) {

}

if ($a & $b) {

}

if ($a ?? $b) {

}

if ($a ? $b : $c) {

}

if ($a xor $b) {

}

if (is_string($a)) {

}

if ($a + ($b ?? 0)) {

}

if ($a + $b) {

}

if ($a . $b) {

}

if ($foo['bar']) {

}

if ($foo->bar) {

}

if (Foo::$bar) {

}

if (Foo::bar()) {

}

if (\is_string($a)) {

}

if ($override ?? [0 => $a] === [0 => 1]) {

}

if (($override ?? [0 => $a]) === [0 => 1]) {

}

if ($a ? $b === 1 : false) {

}

if ($foo->{$bar}) {

}

if ($foo->$bar) {

}

if (self::$bar) {

}

if (isset($a)) {

}

if ('str' . $b) {

}

if ($a && in_array($x, $list)) {

}

if ($a && $b || $c) {

}

if (( $a === 1 )) {

}

if ($object->$$name) {

}

if ($object->${$name}) {

}

if (Foo::$$name) {

}

if (!$a === $b) {

}

if ($a === 1 | ($b ?? 0)) {

}

if ($a === 1 ^ ($b ?? 0)) {

}



