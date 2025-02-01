<?php // lint >= 8.3

class Whatever
{

	const null C_NULL = null;
	const true C_TRUE = true;
	const false C_FALSE = false;
	const int C_NUMBER = 123;
	const int C_NEGATIVE_NUMBER = -123;
	const float C_FLOAT = 123.456;
	const float C_NEGATIVE_FLOAT = -123.456;
	const array C_ARRAY = ['php'];
	const string C_STRING = 'string';
	const string C_STRING_DOUBLE_QUOTES = "string";
	const string C_NOWDOC = <<<'NOWDOC'
		nowdoc
	NOWDOC;
	const string C_HEREDOC = <<<HEREDOC
		heredoc
	HEREDOC;
	const C_ENUM = SomeEnum::VALUE;
	const C_CONSTANT = Whatever::STRING;
	const C_CONSTANT_SELF = self::STRING;
	const C_CONSTANT_PARENT = parent::STRING;

}
