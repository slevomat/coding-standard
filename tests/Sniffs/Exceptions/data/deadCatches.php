<?php

namespace DeadCatches;

use Throwable as DifferentNameForThrowable;

try {

} catch (Throwable $e) {

}

try {

} catch (\Throwable $e) {

}

try {

} catch (DifferentNameForThrowable $e) {

}

try {

} catch (\DifferentNameForThrowable $e) {

}

try {

} catch (FooException $e) {

} catch (Throwable $e) {

} catch (BarException $e) {

} catch (BazException $e) {

}

try {

} catch (\FooException $e) {

} catch (\Throwable $e) {

} catch (\BarException $e) {

} catch (\BazException $e) {

}

try {

} catch (FooException $e) {

} catch (DifferentNameForThrowable $e) {

} catch (BarException $e) {

} catch (BazException $e) {

}

try {

} catch (\FooException $e) {

} catch (\DifferentNameForThrowable $e) {

} catch (\BarException $e) {

} catch (\BazException $e) {

}
