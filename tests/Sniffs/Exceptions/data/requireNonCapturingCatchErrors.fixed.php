<?php // lint >= 8.0

try {

} catch (Throwable) {

}

try {

} catch (Throwable) {
	call(function ($e) {
		return $e;
	}, 'something');
}

try {

} catch (
	InvalidArgumentException
	| OutOfBoundsException
) {

}
