<?php // lint >= 8.0

try {

} catch (Throwable $e) {

}

try {

} catch (Throwable $e) {
	call(function ($e) {
		return $e;
	}, 'something');
}

try {

} catch (
	InvalidArgumentException
	| OutOfBoundsException $e
) {

}
