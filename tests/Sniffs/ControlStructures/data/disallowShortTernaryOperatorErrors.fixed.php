<?php

$x = $a ? $a : true;

sprintf('%s', $x ? $x : 'string');

$z = isset($x) ?: false;
