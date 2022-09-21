<?php

$a = 1;
\assert(\is_int($a) && $a > 0);

$aa = null;
\assert((\is_int($aa) && $aa > 0) || $aa === null);

$b = -1;
\assert(\is_int($b) && $b < 0);

$bb = null;
\assert((\is_int($bb) && $bb < 0) || $bb === null);

$c = 0;
\assert(\is_int($c) && $c >= 0 && $c <= 100);

$cc = null;
\assert((\is_int($cc) && $cc >= 0 && $cc <= 100) || $cc === null);

$d = 0;
\assert(\is_int($d) && $d <= 100);

$dd = null;
\assert((\is_int($dd) && $dd <= 100) || $dd === null);

$e = 0;
\assert(\is_int($e) && $e >= 50);

$ee = null;
\assert((\is_int($ee) && $ee >= 50) || $ee === null);
