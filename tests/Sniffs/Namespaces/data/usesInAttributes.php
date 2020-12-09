<?php

use Something;
use Anything;
use const PHP_VERSION;
use Nothing;

#[Something(Anything::TARGET_CLASS | \Whatever\Anything::IS_REPEATABLE, PHP_VERSION, parameter1: 123, parameter2: [Nothing::SOMETHING, 'string'])]
final class AttributeOverride
{
}
