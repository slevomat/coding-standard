<?php // lint >= 7.4

$a = fn ($aa) => fn ($bb): int => fn ($cc): int => $aa + $bb + $cc;
