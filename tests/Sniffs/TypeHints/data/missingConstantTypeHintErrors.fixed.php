<?php // lint >= 8.3

namespace PersonalHomePage;

class A
{

	const null AA = null;
	const true AAA = true;
	const false AAAA = false;
	const string AAAAA = 'aa';
	const int AAAAAA = 123;
	const float AAAAAAA = 123.456;
	const array AAAAAAAA = ['php'];
	const int AAAAAAAAA = -123;
	const float AAAAAAAAAA = -123.456;

}

interface B
{

	public const null BB = null;
	public const true BBB = true;
	public const false BBBB = false;
	public const string BBBBB = 'aa';
	public const int BBBBBB = 123;
	public const float BBBBBBB = 123.456;
	public const array BBBBBBBB = ['php'];
	public const int BBBBBBBBB = -123;
	public const float BBBBBBBBBB = -123.456;

}

new class implements B
{

	const null CC = null;
	const true CCC = true;
	const false CCCC = false;
	const string CCCCC = 'aa';
	const int CCCCCC = 123;
	const float CCCCCCC = 123.456;
	const array CCCCCCCC = ['php'];
	const int CCCCCCCCC = -123;
	const float CCCCCCCCCC = -123.456;

};

abstract class C
{

	const null DD = null;
	const true DDD = true;
	const false DDDD = false;
	const string DDDDD = 'aa';
	const int DDDDDD = 123;
	const float DDDDDDD = 123.456;
	const array DDDDDDDD = ['php'];
	const int DDDDDDDDD = -123;
	const float DDDDDDDDDD = -123.456;

}
