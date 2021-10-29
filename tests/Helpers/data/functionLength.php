<?php

class FooClass
{

	public function countMe(): string
	{
		/*
		   block comment
		*/
		1 + 1; // slash comment
		# hash comment

		return 'This is the only line that matters (by default)'; /* inline comment */
	/* inline comment */ }
}
