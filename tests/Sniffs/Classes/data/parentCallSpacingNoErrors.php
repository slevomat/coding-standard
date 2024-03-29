<?php // lint >= 8.1

class X extends Whatever
{
	function a() {
		parent::a();

		echo 'wow';
	}

	function b() {
		echo 'wow';

		parent::b();
	}

	function c() {
		$x = parent::c();
		echo $x;
	}

	function d() {
		wow(
			parent::c()
		);
		echo $x;
	}

	function e() {
		return parent::e();
	}

	function f() {
		return (bool) parent::f();
	}

	function g() {
		yield parent::g();
	}

	function h() {
		yield from parent::e();
	}

	function i() {
		return [...parent::f()];
	}

	function j() {
		return doAnything() && parent::j();
	}

	function k(): parent {
	}

	function l()
	{
		return '"' . parent::l() . '"';
	}

	public function m(): object
	{
		return true
			? parent::foo()
			: self::bar();
	}

	public function n(): object
	{
		return true ?? parent::foo();
	}

	public function o(): object
	{
		return true ?: parent::foo();
	}

	public function p()
	{
		return @parent::foo();
	}

	public function q()
	{
		return match (true) {
		    false => parent::q(),
		    default => parent::q(),
		};
	}

}
