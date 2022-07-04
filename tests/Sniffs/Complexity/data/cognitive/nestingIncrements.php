<?php

function nestingIncrements() {
	if ( true ) { // +1
		if ( true ) { // +2 (+1 for nesting)
		}
		if ( true ) { // +2 (+1 for nesting)
		}
	}
}
