<?php // lint >= 8.3

namespace PersonalHomePage;

class A
{

	const AA = null;
	const AAA = true;
	const AAAA = false;
	const AAAAA = 'aa';
	const AAAAAA = 123;
	const AAAAAAA = 123.456;
	const AAAAAAAA = ['php'];
	const AAAAAAAAA = -123;
	const AAAAAAAAAA = -123.456;

}

interface B
{

	public const BB = null;
	public const BBB = true;
	public const BBBB = false;
	public const BBBBB = 'aa';
	public const BBBBBB = 123;
	public const BBBBBBB = 123.456;
	public const BBBBBBBB = ['php'];
	public const BBBBBBBBB = -123;
	public const BBBBBBBBBB = -123.456;

}

new class implements B
{

	const CC = null;
	const CCC = true;
	const CCCC = false;
	const CCCCC = 'aa';
	const CCCCCC = 123;
	const CCCCCCC = 123.456;
	const CCCCCCCC = ['php'];
	const CCCCCCCCC = -123;
	const CCCCCCCCCC = -123.456;

};

abstract class C
{

	const DD = null;
	const DDD = true;
	const DDDD = false;
	const DDDDD = 'aa';
	const DDDDDD = 123;
	const DDDDDDD = 123.456;
	const DDDDDDDD = ['php'];
	const DDDDDDDDD = -123;
	const DDDDDDDDDD = -123.456;

}
