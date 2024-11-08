<?php

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

}
