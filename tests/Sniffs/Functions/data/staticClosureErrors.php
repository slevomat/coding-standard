<?php // lint >= 7.4

function ()
{

};

fn ($a) => $a;

class Whatever
{

	public function doSomething()
	{
		return array_map(function ($i) {
			return preg_replace_callback('(.*)', function (array $matches) {
				return $matches[1];
			}, $i);
		}, []);
	}

}
