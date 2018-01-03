<?php

class ClassWithWriteOnlyProperties
{

	private $p1;
	private $p2 = 0;
	private $p3 = 0;
	private $p4 = 0;
	private $p5 = 0;
	private $p6 = 0;
	private $p7 = 0;
	private $p8 = 0;
	private $p9 = 0;
	private $p10 = 0;
	private $p11 = 0;
	private $p12 = 0;
	private $p13 = '';

	public function add()
	{
		$this->p1 = 1;
		$this->p2 += 1;
		$this->p3 -= 1;
		$this->p4 *= 1;
		$this->p5 /= 1;
		$this->p6 **= 1;
		$this->p7 %= 1;
		$this->p8 &= 1;
		$this->p9 |= 1;
		$this->p10 ^= 1;
		$this->p11 <<= 1;
		$this->p12 >>= 1;
		$this->p13 .= '1';
	}

}
