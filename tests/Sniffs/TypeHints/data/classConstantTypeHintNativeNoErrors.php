<?php // lint >= 8.3

namespace SomeNamespace;

const IGNORED = 'ignored';

class Whatever
{

	const null C_NULL = null;
	const true C_TRUE = true;
	const false C_FALSE = false;
	const string C_STRING = 'aa';
	const int C_NUMBER = 123;
	const int C_NEGATIVE_NUMBER = -123;
	const float C_FLOAT = 123.456;
	const float C_NEGATIVE_FLOAT = -123.456;
	const array C_ARRAY = ['php'];
	const SomeEnum C_ENUM = SomeEnum::VALUE;
	const string C_CONSTANT = Whatever::STRING;

}
