<?php

namespace XXX;

use Error;
use Throwable;
use YYY\Partial;
use YYY\UsedException;

try {
	throw new \XXX\SameNamespaceException();
} catch (\XXX\SameNamespaceException $e) {

}

throw new \YYY\UsedException();

throw new \YYY\Partial\PartialyUsedException();

throw new \YYY\AlreadyFqnException();

try {
	throw new \Error();
} catch (\Throwable $e) {

}
