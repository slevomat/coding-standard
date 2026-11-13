<?php

$a = $_GET['a'] ?? 'a';

$b = $bb ?? 'bb';

$c = $cc['c'] ?? 'c';

$d = $e ?? 'e';
$dd = $ee ?? 'ee';

$f = $g ?? 'g';
$ff = $gg ?? 'gg';

$h = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} ?? 1;

$i = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} ?? 0;
$j = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} ?? false;

$k = $this
		->${'a'}[0]->$$b[1][2]
		::$c[3][4][5]->{" $d"} ?? true;

$l = \Whatever\Something::$anything ?? 1;

$m = $object->anything ?? 0;

$n = ($something ?? false);

$o[$something ?? true] = true;

$p = doSomething()() ?? false;

$q[$something ?? ($anything ?? 'default')] = true;
$r[$something ?? ($anything !== false ? $anything : 'default')] = true;

function ($options) {
	return $options['value'] ?? 1;
};

$s = $a ?? ($b ? 'x' : 'y');
$t = $a ?? ([$b][0] ? 'x' : 'y');
$u = $a ?? (${'b'} ? 'x' : 'y');
echo $a ?? 'default';
echo $a ?? 'default';
$out .= $a ?? 'default';
$out .= $a ?? 'default';
$v = [$a ?? [1, 2], 'foo'];

$s2 = $a ?? ($b ? 'x' : 'y') /* keep */;
$s3 = $a ?? ($b ? 'x' : 'y') // keep
;

function () use ($a) {
	yield $a ?? 'default';
	yield from $a ?? 'default';
	yield $a ?? 'default';
	yield from $a ?? 'default';
};

foo(); $a ?? 'fallback';
foo(); $a ?? 'default';
if ($flag) { $a ?? 'fallback'; }
if ($flag) { $a ?? 'default'; }

$result = $ready ? $a ?? 'default' : null;
include $path ?? 'default.php';
require_once $path ?? 'default.php';
$r = $a ?? (fn () => 1) and $b = 1;
$r = $a ?? (include 'a.php') and $b = 1;
$r = $a ?? ($b and $c);
$r = $a ?? function () { $x = 1; };
$s4 = $a ?? $b // keep
;
$s5 = $a ?? $b # keep
;
$s6 = $a ?? $b /* keep */
;
$s7 = $a ?? $b // keep

;


