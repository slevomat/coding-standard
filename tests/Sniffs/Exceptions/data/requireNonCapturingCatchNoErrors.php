<?php // lint >= 8.0

try {

} catch (Throwable) {

}

try {

} catch (Throwable $e) {
	$a = $e;
	rollback($e);
}

try {
} catch (\ErrorException $exception) {
	echo "Exception message: {$exception->getMessage()}";
}

function () {
	try {
	} catch (\ErrorException $exception) {
	} finally {
		// Nothing
	}

	if (isset($exception)) {

	}
};

function () {
	try {
	} catch (\ErrorException $exception) {
		try {

		} catch (\Throwable) {

		}

		echo $exception;
	}

};
