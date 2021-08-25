<?php // lint >= 8.0

try {

} catch (Throwable) {

}

try {
} catch (\ErrorException) {
	echo 'Exception';
}

function () {
	try {
	} catch (
		\ErrorException |
		Exception
	) {
	} finally {
		// Nothing
	}
};
