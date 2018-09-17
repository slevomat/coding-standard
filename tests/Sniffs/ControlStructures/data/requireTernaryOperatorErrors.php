<?php

function () {
	if (true) {
		return true;
	} else {
		return false;
	}
};

if (doSomething()) {
	$a = 'a';
} else {
	$a = 'aa';
}

class Whatever
{

	public function __construct()
	{
		if (true) {
			$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"} = true;
		} else {
			$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"} = false;
		}
	}

}

if (! (
		$newValue instanceof GhostObjectInterface
		&& ! $newValue->isProxyInitialized()
	)
	&& $association->getInversedBy()
	&& $association instanceof OneToOneAssociationMetadata
) {
	$a = 'a';
} else {
	$a = 'aa';
}
