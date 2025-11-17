<?php

class Whatever
{
	public function __construct()
	{
		$this->reportError(
			'false',
			true
		);
		$this->dontReportError('true', false);
	}
}

function () {
	reportError(
		'false',
		true
	);
	dontReportError('true', false);
};
