<?php

try {

} catch (Throwable $e) {

}

try {
} catch (\ErrorException $exception) {
	echo "Exception message: {$exception->getMessage()}";
}

function () {
	try {
	} catch (
		\ErrorException |
		Exception $exception
	) {
	} finally {
		// Nothing
	}
};
