<?php // lint >= 7.1

try {
	foo();
} catch (FooException | \Exception $e) { // ok - catching \Throwable later

} catch (FooException | Exception $e) { // ok - catching \Throwable later

} catch (\Throwable $e) {

}

try {
	foo();
} catch (FooException | \Exception $e) { // ok - catching \Throwable later

} catch (FooException | Exception $e) { // ok - catching \Throwable later

} catch (FooException | \Throwable $e) {

}

try {

} catch (FooException | \Exception $e) {

} catch (FooException | Exception $e) {

}
