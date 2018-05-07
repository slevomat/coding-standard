<?php

$x = $a ?: true;

sprintf('%s', $x ?: 'string');

$z = isset($x) ?: false;
