<?php

$a = $b ? 1 : 0;

$c = $d ? 1 : 0;

class Whatever
{

	public function __construct($parameter)
	{
		$x = self::$a ? 1 : 0;
		$x = static::$a ? 1 : 0;
		$x = self::$$parameter ? 1 : 0;
		$x = parent::${'a'} ? 1 : 0;
		$x = self::${'a'}[0] ? 1 : 0;
		$x = Anything::$a ? 1 : 0;
		$x = Something\Anything::$a ? 1 : 0;
		$x = \Something\Anything::$a ? 1 : 0;
		$x = self::$a::$b ? 1 : 0;
		$x = $this::$a ? 1 : 0;
		$x = $this->a ? 1 : 0;
		$x = $this->$$parameter ? 1 : 0;
		$x = $this->{'a'} ? 1 : 0;
		$x = $$parameter ? 1 : 0;
		$x = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"} ? 1 : 0;
		$x = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"}() ? 1 : 0;
		$x = $this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"}()()()() ? 1 : 0;
		$x = !$this->${'a'}[0]->$$b[1][2]::$c[3][4][5]->{" $d"}()()()() ? 1 : 0;
		$x = isset($xxx) ? 1 : 0;
		$x = !isset($xxx) ? 1 : 0;
		$x = empty($xxx) ? 1 : 0;
		$x = !empty($xxx) ? 1 : 0;
	}

}
