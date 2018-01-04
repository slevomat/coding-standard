<?php

namespace Foo;

use DateTime;
use DateTimeImmutable;
use function phpversion;

new DateTimeImmutable();

echo DateTime::RFC3339;

phpversion();

echo \PHP_VERSION;
