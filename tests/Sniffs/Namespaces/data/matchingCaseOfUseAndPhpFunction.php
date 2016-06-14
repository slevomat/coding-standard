<?php declare(strict_types = 1);

use Doctrine\ORM\Query\Expr\Join;

class TestJoin
{

	public function testJoin()
	{
		$arr[0] = '1';
		$arr[2] = '2';
		$arr[3] = '3';

		join('item', $arr);
		Join::foo();
	}

}
