<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces\data\UsesSortedByLengthSniff;

// Comment to ignore

use A;
// Comment
// Second comment
use D\E\F;
use H\I\J;
/*
 * Comment
 */
use H\I\K;
use L\M\O;
use P\Q\R\S;
// phpcs:ignore
use P\Q\R\T;
use U\V\W\Z;
use U\V\X\Y;
use B\C as Ccc;
use L\m\O as Ooo;
use function X\boo;
use function X\foo;
use function strpos;
use const X\BOO;
use const X\FOO;
use const PHP_OS;

class fixableSortedByLengthUsesWithComments
{

}
