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
	if (!((true && true) || false)) {
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
