<?php // lint >= 7.4

$a = fn ($aa) => fn ($bb) => fn ($cc) => $aa + $bb + $cc;
