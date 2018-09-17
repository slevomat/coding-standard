<?php

function () {
	return true ? true : false;
};

$a = doSomething() ? 'a' : 'aa';

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
