<?php

function () {
	return true ? true : false;
};

function () {
	if (true) {
		// Comment
		return true;
	} else {
		return false;
	}
};

function () {
	if (true) {
		return true;
	} else {
		// Comment
		return false;
	}
};

$a = doSomething() ? 'a' : 'aa';

if (doAnything()) {
	// Comment
	$a = 'a';
} else {
	$a = 'aa';
}

if (doNothing()) {
	$a = 'a';
} else {
	// Comment
	$a = 'aa';
}

class Whatever
{

	public function __construct()
	{
		$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"} = true ? true : false;
	}

}

$a = ! (
		$newValue instanceof GhostObjectInterface
		&& ! $newValue->isProxyInitialized()
	)
	&& $association->getInversedBy()
	&& $association instanceof OneToOneAssociationMetadata
 ? 'a' : 'aa';
