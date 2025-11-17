<?php

class Whatever
{
	public function __construct()
	{
		$this->doAnything('false');
		$this->doAnything('true', false);
	}
}

function ($text) {
	sprintf(_('one parameter'));
	return sprintf(_('one parameter'), $text);
};
