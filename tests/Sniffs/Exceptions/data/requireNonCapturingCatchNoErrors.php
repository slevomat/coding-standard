<?php // lint >= 8.0

try {

} catch (Throwable) {

}

try {

} catch (Throwable $e) {
	rollback($e);
}

try {
} catch (\ErrorException $exception) {
	echo "Exception message: {$exception->getMessage()}";
}
