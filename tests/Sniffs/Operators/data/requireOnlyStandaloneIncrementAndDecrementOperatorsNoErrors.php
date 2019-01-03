<?php

$x++;

function ($x) {
	++$x;
};

if (true) {
	$x--;
}

for ($i = 0; $i < 10; $i++) {

}

for ($i = 0; $i < 10; ++$i) {

}

for ($i = 10; $i > 0; $i--) {

}

for ($i = 10; $i > 0; --$i) {

}

$x++;
++$x;
$x--;
--$x;

$a['a']++;
++$a['a'];
$a['a']--;
--$a['a'];

$this->typeCounter[$value]++;

switch ($foo) {
	case 1:
		$bar++;
		break;
}
