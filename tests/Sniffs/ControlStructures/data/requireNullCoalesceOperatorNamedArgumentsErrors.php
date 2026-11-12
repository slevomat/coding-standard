<?php // lint >= 8.0

render(value: isset($a) ? $a : 'default');
render(value: $a === null ? 'default' : $a);
