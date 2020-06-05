<?php

function () {
	if (false) {
		return false;
	}

	// Something
};

// Identical condition
function () {
	if ($bool !== true) {
		return;
	}

	// Something
};

// Not identical condition
foreach ($items as $item) {
	if ($item === null) {
		continue;
	}

	// Something
}

// Equal condition
while (true) {
	if ($string != '') {
		break;
	}

	// Something
}

// Not equal condition
do {
	if ($string == '') {
		throw new Exception();
	}

	// Something
} while (true);

function greateThanOrEqualCondition() {
	if ($number < 0) {
		return [];
	}

	// Something
}

function greateThanCondition() {
	if ($number <= 0) {
		exit;
	}

	// Something
}

function lessThanOrEqualCondition() {
	if ($number > 0) {
		die;
	}

	// Something
}

function lessThanCondition() {
	if ($number >= 0) {
		return;
	}

	// Something
}

function simpleCondition($password) {
	if (!$password->isValid()) {
		return false;
	}

	// Something
}

function negativeCondition($token) {
	if ($token->isExpired()) {
		return false;
	}

	// Something
}

function instanceOfCondition($e) {
	if (!($e instanceof Exception)) {
		return;
	}

	logError($e);
}

function noSemicolonInIfScope() {
	if (false) {
		return;
	}

	if (true) {
		return;
	}

	// Something
}

function ifAtTheEndOfFunction() {
	$result = doSomething();
	if (!$result) {
		return;
	}

	doMore();
}

while (true) {
	$result = doSomething();
	if (!$result) {
		continue;
	}

	doMore();
}

do {
	$result = doSomething();
	if (!$result) {
		continue;
	}

	doMore();
} while (true);

foreach ($items as $item) {
	if ($item === null) {
		continue;
	}

	doSomething();
}

for ($i = 0; $i < 100; $i++) {
	if ($i % 2 !== 0) {
		continue;
	}

	doSomething();
}

function logicalAndCondition($nullableString, $e) {
	if (false || $nullableString === null || $e instanceof Exception) {
		return;
	}

	doSomething();
}

function logicalOrCondition($e, $number) {
	if (false && !($e instanceof Exception) && $number > 0) {
		return;
	}

	doSomething();
}

function indentedBySpaces() {
    if (false) {
        return;
    }

    doSomething();
}

function ifHasExitCodeToo() {
	if (true) {
		return true;
	}

	return false;
}

function exitCodeIsNotOnFirstLineOfScope() {
	if (true) {
		doSomething();
		return true;
	}

	doSomethingElse();
	return false;
}

function inlineCommentAfterIf() {
	if (false) {
		return false;
	}
	// Comment
	doSomething();
}

function logicalCombinedCondition() {
	if ((false || false) && true) {
		return;
	}

	doSomething();
}

function logicalXorCondition() {
	if (!(true xor false)) {
		return;
	}

	doSomething();
}

function negativeConditionWithFunctionCall() {
	if (preg_match($regex, $value)) {
		return;
	}

	$this->context->buildViolation($constraint->message)
		->setParameter('%string%', $value)
		->addViolation();
}

function negativeConditionWithMethodCall() {
	foreach (array_reverse($this->getReflectionService()->getParentClasses($name)) as $parentClass) {
		if ($this->getDriver()->isTransient($parentClass)) {
			continue;
		}

		$parentClasses[] = $parentClass;
	}
}

function binaryAndCondition() {
	if (!($invoke & INVOKE_MANAGER)) {
		return;
	}

	$this->eventManager->dispatchEvent($eventName, $event);
}

function negativeLogicalAndCondition() {
	if (isset($parameterMappings[$parameterName]) && array_key_exists($parameterName, $parameterMappings)) {
		return;
	}

	unset($parameters[$key]);
}

function negativeLogicalOrCondition() {
	if (isset($parameterMappings[$parameterName]) || array_key_exists($parameterName, $parameterMappings)) {
		return;
	}

	unset($parameters[$key]);
}

function negativeLogicalConditionOnMoreLines() {
	if (
		isset($parameterMappings[$parameterName])
		&& array_key_exists($parameterName, $parameterMappings)
	) {
		return;
	}

	unset($parameters[$key]);
}

function twoNegativeConditions() {
	if (isset($data[$fieldName]) && $valueIsNull) {
		return;
	}

	$data[$fieldName] = $value;
}

function twoNegetiveConditionsOnTwoLines() {
	if (
		isset($data[$fieldName])
		&& $valueIsNull
	) {
		return;
	}

	$data[$fieldName] = $value;
}

function commentInCondition() {
	if (
		// Comment
		!isset($a)
		/*
		 Comment
		 */
		|| $a !== 'a'
		// Comment
		|| $a === 'b'
	) {
		return;
	}

	doSomething();
}

function veryUglyConditionFromDoctrine() {
	foreach ($class->getDeclaredPropertiesIterator() as $field => $association) {
		if (!$association->getInversedBy()
			|| !($association instanceof OneToOneAssociationMetadata)
			// @TODO refactor this
			// we don't want to set any values in un-initialized proxies
			|| (
				$newValue instanceof GhostObjectInterface
				&& ! $newValue->isProxyInitialized()
			)
		) {
			continue;
		}

		$inverseAssociation = $targetClass->getProperty($association->getInversedBy());

		$inverseAssociation->setValue($newValue, $entity);
	}
}

function veryUglyConditionFromDoctrineWithALittleChange() {
	foreach ($class->getDeclaredPropertiesIterator() as $field => $association) {
		if ((
				$newValue instanceof GhostObjectInterface
				&& ! $newValue->isProxyInitialized()
			)
			|| !$association->getInversedBy()
			|| !($association instanceof OneToOneAssociationMetadata)
		) {
			continue;
		}

		$inverseAssociation = $targetClass->getProperty($association->getInversedBy());

		$inverseAssociation->setValue($newValue, $entity);
	}
}

function allConditionsWithEarlyExit() {
	if ($dateTime instanceof DateTimeImmutable) {
		return true;
	}

	if ($dateTime instanceof DateTime) {
		return true;
	}

	if (is_numeric($dateTime)) {
		return true;
	}

	if (is_string($dateTime)) {
		throw new NotImplementedException();
	}

	throw new NotImplementedException();
}

function allConditionsWithEarlyExitButWithoutElse($dateTime) {
	if ($dateTime instanceof DateTimeImmutable) {
		return true;
	}

	if ($dateTime instanceof DateTime) {
		return true;
	}

	if (is_numeric($dateTime)) {
		return true;
	}

	if (is_string($dateTime)) {
		throw new NotImplementedException();
	}
}

function ifElseInElse() {
	if (false) {
		if (true) {
			return;
		}

		if (false) {
			return;
		}

		return;
	}

	doSomething();
}

function logicalCombinedComplicatedCondition() {
	foreach ($records as $record) {
		if (!in_array($record['type'], ['A', 'C']) || (strpos($record['name'], 'www.') !== 0 && strpos($record['name'], 'ftp.') !== 0)) {
			continue;
		}

		echo 'You should see this echo twice!'.PHP_EOL;
	}
}

function logicalVeryComplicatedCondition() {
	if ((false) || ((true && false) && (true || (false)))) {
		return;
	}

	doSomething();
}

function negativeInstanceOf($phpEditor, $jsonEditor) {
	$this->phpEditor = $phpEditor;
	$this->jsonEditor = $jsonEditor;

	if ($this->phpEditor instanceof CallbackReceiver) {
		$this->phpEditor->setCallback($this->getCallback());
	}
	if ($this->jsonEditor instanceof CallbackReceiver) {
		return;
	}

	doSomething();
}

function uselessElse() {
	if (true) {
		return;
	}

	if (false) {
		return;
	}

	echo 'Please do not use else!';
}

function uselessElseIf() {
	if (true) {
		return true;
	}

	if (false) {
		doSomething();
	} else {
		doSomethingElse();
	}
}

function heredoc() {
	foreach ([] as $f) {
		if (!file_exists($f)) {
			continue;
		}

		echo <<<EOF
XYZ
EOF;
	}
}

function nowdoc() {
	foreach ([] as $f) {
		if (!file_exists($f)) {
			continue;
		}

		echo <<<'EOF'
XYZ
EOF;
	}
}

function uselessElseWithHeredoc() {
	if (true) {
		return;
	}

	if (false) {
		return <<<EOF
		XYZ
EOF;
	}

	echo <<<EOF
	XYZ
EOF;
}

function inlineCommentAfterForeach()
{
	foreach ([] as $_) { // Comment
		if (false) {
			continue;
		}

		$x = 1;
	}
}

function moreInlineComments()
{
	if (true) { // Comment
		return 1;
	}

	if (true) { // Comment
		return 2;
	}
	// Comment
	return 3;
}

class Whatever
{
	public function moreInlineCommentsWithMoreIndentation(): int
	{
		if (true) { // Comment
			return 1;
		}

		if (true) { // Comment
			return 2;
		}
		// Comment
		return 3;
	}
}

function nestedIfWhenOneBranchDoesNotHaveEarlyExit($a, $b)
{
	if ($a === 1) {
		if ($b === 1) {
			return doSomething(function () {
				if (true) {
					// Nothing
				}

				return false;
			});
		}

		if ($b === 2) {
			throw new \Exception('2');
		}

		throw new \Exception('anything');
	}
};

function conditionWithNullCoalesceOperator()
{
	foreach ([] as $key => $item) {
		if (!($key ?? false)) {
			continue;
		}

		yield $item;
	}
};

function conditionWithShortTernaryOperator()
{
	foreach ([] as $key => $item) {
		if (!($key ?: false)) {
			continue;
		}

		yield $item;
	}
};

// Simple else - needs to be last
if (true) {
	return true;
}

doSomething();
