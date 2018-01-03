<?php

namespace DeadCatches;

try {

} catch (\FooException $e) {

} catch (\BarException $e) {

} catch (\BooException | \Throwable $e) {

}

try {

} catch (\FooException $e) {

} catch (\BarException $e) {

} catch (\Throwable | \BooException $e) {

}

try {

} catch (\FooException $e) {

} catch (\BooException | \Throwable $e) {

} catch (\BarException $e) {

}

try {

} catch (\FooException $e) {

} catch (\Throwable | \BooException $e) {

} catch (\BarException $e) {

}
