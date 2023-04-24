<?php

$simpleString = 'foo';
$array = ['foo', 'bar', 'baz'];

$object = new \stdClass();
$object->name = 'foobar';

// Covers strings where ${...} syntax is used
$dollarCurlySyntaxDoubleQuotedScalar = "Some double quoted string with scalar variable ${simpleString}.";
$dollarCurlySyntaxDoubleQuotedArrayItem = "Some double quoted string with array item variable ${array[1]}.";

$dollarCurlySyntaxSingleQuotedScalar = 'Some single quoted string with scalar variable ${simpleString}.';
$dollarCurlySyntaxSingleQuotedArrayItem = 'Some single quoted string with array item variable ${array[1]}.';

$dollarCurlySyntaxHereDoc = <<<EOT
Some heredoc line with scalar variable ${simpleString}
Some heredoc line with array item variable ${array[1]}
EOT;

// Covers strings where {$...} syntax is used
$curlyDollarSyntaxDoubleQuotedScalar = "Some double quoted string with scalar variable {$simpleString}.";
$curlyDollarSyntaxDoubleQuotedArrayItem = "Some double quoted string with array item variable {$array[1]}.";
$curlyDollarSyntaxDoubleQuotedObject = "Some double quoted string with object variable {$object->name}.";

$curlyDollarSyntaxSingleQuotedScalar = 'Some single quoted string with scalar variable {$simpleString}.';
$curlyDollarSyntaxSingleQuotedArrayItem = 'Some single quoted string with array item variable {$array[1]}.';
$curlyDollarSyntaxSingleQuotedObject = 'Some single quoted string with object variable {$object->name}.';

$curlyDollarSyntaxHereDoc = <<<EOT
Some heredoc line with scalar variable {$simpleString}
Some heredoc line with array item variable {$array[1]}
Some heredoc line with object variable {$object->name}
EOT;

// Covers strings where $... syntax is used
$simpleSyntaxDoubleQuotedScalar = "Some double quoted string with scalar variable $simpleString.";
$simpleSyntaxDoubleQuotedArrayItem = "Some double quoted string with array item variable $array[1].";
$simpleSyntaxDoubleQuotedObject = "Some double quoted string with object variable $object->name.";

$simpleSyntaxSingleQuotedScalar = 'Some single quoted string with scalar variable $simpleString.';
$simpleSyntaxSingleQuotedArrayItem = 'Some single quoted string with array item variable $array[1].';
$simpleSyntaxSingleQuotedObject = 'Some single quoted string with object variable $object->name.';

$simpleSyntaxHereDoc = <<<EOT
Some heredoc line with scalar variable $simpleString
Some heredoc line with array item variable $array[1]
Some heredoc line with object variable $object->name
EOT;

"{$array[$simpleString]} $simpleString";

"{$a->test($b)}";
