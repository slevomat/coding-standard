<?php // lint >= 7.4

$_GET['a'] = $_GET['a'] ?? 'a';

$bb = $bb ?? 'bb';

$cc['c'] = $cc['c'] ?? 'c';

$e = $e ?? 'e';
$ee = $ee ?? 'ee';

$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} ?? 1;

$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} ?? 0;
$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->xxx->{" $d"} ?? false;

$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"} = $this
		->${'a'}[0]->$$b[1][2]
		::$c[3][4][5]->{" $d"} ?? true;

\Whatever\Something::$anything = \Whatever\Something::$anything ?? 1;

$object->anything = $object->anything ?? 0;
