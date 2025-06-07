<?php

function binaryLogicalOperators() {

	if( true && true ) {} // +1 +1

	if( true || true ) {} // +1 +1

	if( true && true && true ) {} // +1 +1 +0

	if( true || true || true ) {} // +1 +1 +0

	if ( // +1
		true && true && true // +1 +0
		|| true || true // +1 +0
		&& true) {} // +1

	if ( // +1
		true && // +1
		!(true && true)) {} // +1

	true && true && true; // +1 +0

	echo( true && true && true ); // +1 +0

	$foo = true && true && true; // +1 +0

	return true && true && true; // +1 +0
}
