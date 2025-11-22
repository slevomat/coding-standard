<?php // lint >= 8.0

class A
{
	public function __construct(
		#[SensitiveParameter] string $password
	) {
	}
}
