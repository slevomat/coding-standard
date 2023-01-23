<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces\data\UsesSortedByLengthSniff;

// Comment to ignore

use function X\foo;
use const X\FOO;
use \A;
/*
 * Comment
 */
use \H\I\K;
use \H\I\J;
use const PHP_OS;
use \B\C as Ccc;
use L\M\O;
use const X\BOO;
use L\m\O as Ooo;
use function strpos;
// Comment
// Second comment
use D\E\F;
use \U\V\X\Y;
use function X\boo;
// phpcs:ignore
use P\Q\R\T;
use \U\V\W\Z;
use P\Q\R\S;

class fixableSortedByLengthUsesWithComments
{

}
