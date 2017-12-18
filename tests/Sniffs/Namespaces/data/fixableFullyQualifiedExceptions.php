<?php

namespace XXX;

use Error;
use Throwable;
use YYY\Partial;
use YYY\UsedException;

try {
	throw new SameNamespaceException();
} catch (SameNamespaceException $e) {

}

throw new UsedException();

throw new Partial\PartialyUsedException();

throw new \YYY\AlreadyFqnException();

try {
	throw new Error();
} catch (Throwable $e) {

}
