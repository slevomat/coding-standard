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
