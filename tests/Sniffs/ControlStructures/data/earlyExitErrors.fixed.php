<?php

function () {
	if (!true) {
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
		yield [];
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
	if (!true) {
		return;
	}

	if (!false) {
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
	if (!true || $nullableString === null || $e instanceof Exception) {
		return;
	}

	doSomething();
}

function logicalOrCondition($e, $number) {
	if (!true && !($e instanceof Exception) && $number > 0) {
		return;
	}

	doSomething();
}

function indentedBySpaces() {
    if (!true) {
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
	if (!true) {
		return false;
	}
	// Comment
	doSomething();
}

function logicalCombinedCondition() {
	if ((!true || !true) && !false) {
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
	if (!true) {
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
	if ((!true) || ((!false && !true) && (!false || (!true)))) {
		return;
	}

	doSomething();
}

function yieldFrom() {
	if ($number < 0) {
		yield from [];
	}

	// Something
}
