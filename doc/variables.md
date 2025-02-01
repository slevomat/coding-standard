## Variables

#### SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable

Disallows use of super global variables.

#### SlevomatCodingStandard.Variables.DisallowVariableVariable

Disallows use of variable variables.

#### SlevomatCodingStandard.Variables.DuplicateAssignmentToVariable

Looks for duplicate assignments to a variable.

#### SlevomatCodingStandard.Variables.UnusedVariable

Looks for unused variables.

Sniff provides the following settings:

* `ignoreUnusedValuesWhenOnlyKeysAreUsedInForeach` (default: `false`): ignore unused `$value` in foreach when only `$key` is used

```php
foreach ($values as $key => $value) {
	echo $key;
}
```

#### SlevomatCodingStandard.Variables.UselessVariable 🔧

Looks for useless variables.
