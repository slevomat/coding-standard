<?php // lint >= 8.3

class Whatever
{

	const C_NULL = null;
	const C_TRUE = true;
	const C_FALSE = false;
	const C_NUMBER = 123;
	const C_NEGATIVE_NUMBER = -123;
	const C_FLOAT = 123.456;
	const C_NEGATIVE_FLOAT = -123.456;
	const C_ARRAY = ['php'];
	const C_STRING = 'string';
	const C_STRING_DOUBLE_QUOTES = "string";
	const C_NOWDOC = <<<'NOWDOC'
		nowdoc
	NOWDOC;
	const C_HEREDOC = <<<HEREDOC
		heredoc
	HEREDOC;
	const C_ENUM = SomeEnum::VALUE;
	const C_CONSTANT = Whatever::STRING;
	const C_CONSTANT_SELF = self::STRING;
	const C_CONSTANT_PARENT = parent::STRING;

}
