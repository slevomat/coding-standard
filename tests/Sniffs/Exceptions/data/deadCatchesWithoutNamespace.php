<?php

try {

} catch (FooException $e) {

} catch (Throwable $e) {

} catch (BarException $e) {

} catch (BazException $e) {

}

try {

} catch (FooException $e) {

} catch (BarException $e) {

} catch (Throwable $e) {

} catch (Throwable $e) {

}

try {

} catch (\FooException $e) {

} catch (\Throwable $e) {

} catch (\BarException $e) {

} catch (\BazException $e) {

}

try {

} catch (\FooException $e) {

} catch (\BarException $e) {

} catch (\Throwable $e) {

} catch (\Throwable $e) {

}
