<?php

$a = "{$foo(\Exception::class)}";
$b = "{$foo(new \Some\ClassName)}";
$c = <<<EOT
{$foo(new \Another\ClassName)}
EOT;
$d = "{$foo($var::method)}";
