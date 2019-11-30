<?php // lint >= 7.4

$variable = 'variable';

class Whatever
{

	private $withoutTypeHint = 'false';

	private ?string $nullable = 'string';

	private int $notNullable = 0;

}
