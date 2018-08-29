<?php

$a = $_GET['a'] ?? 'a';

if (isset($b)) {

}

$c = isset($cc, $ccc) ? 'c' : 'cc';

$d = !isset($dd) ? 'd' : 'dd';

$e = isset($ee) ? $eee : 'eee';

$d = $e !== null ? 'e' : $e;
$dd = null !== $ee ? 'ee' : $ee;

$f = $g === null ? $g : 'g';
$ff = null === $gg ? $gg : 'gg';

$h = isset($this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"}) ? $this->${'AAAAAAAA'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} : null;

$i = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} !== null ? null : $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"};
$j = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} === null ? $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} : null;

$k = $g !== true ? $g : null;
$l = false === $g ? null : $g;

if ($m === null) {

}

$n = true === null ? true : false;

$o = $oo !== null ? $oo->property : null;

$p = $pp === null ? null : $pp + 1;

$q = $r !== null && $s !== null
	? $s
	: '';

$t = $t && isset($v) ? $v : null;
$t = $t || isset($v) ? $v : null;
$t = $t and isset($v) ? $v : null;
$t = $t or isset($v) ? $v : null;
$t = $t xor isset($v) ? $v : null;
