<?php

function () {
	if (true) {
		// Something
	} else {
		return false;
	}
};

// Identical condition
function () {
	if ($bool === true) {
		// Something
	} else {
		return;
	}
};

// Not identical condition
foreach ($items as $item) {
	if ($item !== null) {
		// Something
	} else {
		continue;
	}
}

// Equal condition
while (true) {
	if ($string == '') {
		// Something
	} else {
		break;
	}
}

// Not equal condition
do {
	if ($string != '') {
		// Something
	} else {
		throw new Exception();
	}
} while (true);

function greateThanOrEqualCondition() {
	if ($number >= 0) {
		// Something
	} else {
		return [];
	}
}

function greateThanCondition() {
	if ($number > 0) {
		// Something
	} else {
		exit;
	}
}

function lessThanOrEqualCondition() {
	if ($number <= 0) {
		// Something
	} else {
		die;
	}
}

function lessThanCondition() {
	if ($number < 0) {
		// Something
	} else {
		return;
	}
}

function simpleCondition($password) {
	if ($password->isValid()) {
		// Something
	} else {
		return false;
	}
}

function negativeCondition($token) {
	if (!$token->isExpired()) {
		// Something
	} else {
		return false;
	}
}

function instanceOfCondition($e) {
	if ($e instanceof Exception) {
		logError($e);
	} else {
		return;
	}
}

function noSemicolonInIfScope() {
	if (true) {
		if (false) {
			// Something
		}
	} else {
		return;
	}
}

function ifAtTheEndOfFunction() {
	$result = doSomething();
	if ($result) {
		doMore();
	}
}

while (true) {
	$result = doSomething();
	if ($result) {
		doMore();
	}
}

do {
	$result = doSomething();
	if ($result) {
		doMore();
	}
} while (true);

foreach ($items as $item) {
	if ($item !== null) {
		doSomething();
	}
}

for ($i = 0; $i < 100; $i++) {
	if ($i % 2 === 0) {
		doSomething();
	}
}

function logicalAndCondition($nullableString, $e) {
	if (true && $nullableString !== null && !($e instanceof Exception)) {
		doSomething();
	} else {
		return;
	}
}

function logicalOrCondition($e, $number) {
	if (true || $e instanceof Exception || $number <= 0) {
		doSomething();
	} else {
		return;
	}
}

function indentedBySpaces() {
    if (true) {
        doSomething();
    } else {
        return;
    }
}

function ifHasExitCodeToo() {
	if (true) {
		return true;
	} else {
		return false;
	}
}

function exitCodeIsNotOnFirstLineOfScope() {
	if (true) {
		doSomething();
		return true;
	} else {
		doSomethingElse();
		return false;
	}
}

function inlineCommentAfterIf() {
	if (true) { // Comment
		doSomething();
	} else {
		return false;
	}
}

function logicalCombinedCondition() {
	if ((true && true) || false) {
		doSomething();
	} else {
		return;
	}
}

function logicalXorCondition() {
	if (true xor false) {
		doSomething();
	} else {
		return;
	}
}

function negativeConditionWithFunctionCall() {
	if (!preg_match($regex, $value)) {
		$this->context->buildViolation($constraint->message)
			->setParameter('%string%', $value)
			->addViolation();
	}
}

function negativeConditionWithMethodCall() {
	foreach (array_reverse($this->getReflectionService()->getParentClasses($name)) as $parentClass) {
		if (!$this->getDriver()->isTransient($parentClass)) {
			$parentClasses[] = $parentClass;
		}
	}
}

function binaryAndCondition() {
	if ($invoke & INVOKE_MANAGER) {
		$this->eventManager->dispatchEvent($eventName, $event);
	}
}

function negativeLogicalAndCondition() {
	if (! (isset($parameterMappings[$parameterName]) && array_key_exists($parameterName, $parameterMappings))) {
		unset($parameters[$key]);
	}
}

function negativeLogicalOrCondition() {
	if (! (isset($parameterMappings[$parameterName]) || array_key_exists($parameterName, $parameterMappings))) {
		unset($parameters[$key]);
	}
}

function negativeLogicalConditionOnMoreLines() {
	if (! (
		isset($parameterMappings[$parameterName])
		&& array_key_exists($parameterName, $parameterMappings)
	)) {
		unset($parameters[$key]);
	}
}

function twoNegativeConditions() {
	if (! isset($data[$fieldName]) || ! $valueIsNull) {
		$data[$fieldName] = $value;
	}
}

function twoNegetiveConditionsOnTwoLines() {
	if (
		! isset($data[$fieldName])
		|| ! $valueIsNull
	) {
		$data[$fieldName] = $value;
	}
}

function commentInCondition() {
	if (
		// Comment
		isset($a)
		/*
		 Comment
		 */
		&& $a === 'a'
		// Comment
		&& $a !== 'b'
	) {
		doSomething();
	}
}

function veryUglyConditionFromDoctrine() {
	foreach ($class->getDeclaredPropertiesIterator() as $field => $association) {
		if ($association->getInversedBy()
			&& $association instanceof OneToOneAssociationMetadata
			// @TODO refactor this
			// we don't want to set any values in un-initialized proxies
			&& ! (
				$newValue instanceof GhostObjectInterface
				&& ! $newValue->isProxyInitialized()
			)
		) {
			$inverseAssociation = $targetClass->getProperty($association->getInversedBy());

			$inverseAssociation->setValue($newValue, $entity);
		}
	}
}

function veryUglyConditionFromDoctrineWithALittleChange() {
	foreach ($class->getDeclaredPropertiesIterator() as $field => $association) {
		if (! (
				$newValue instanceof GhostObjectInterface
				&& ! $newValue->isProxyInitialized()
			)
			&& $association->getInversedBy()
			&& $association instanceof OneToOneAssociationMetadata
		) {
			$inverseAssociation = $targetClass->getProperty($association->getInversedBy());

			$inverseAssociation->setValue($newValue, $entity);
		}
	}
}

function allConditionsWithEarlyExit() {
	if ($dateTime instanceof DateTimeImmutable) {
		return true;
	} elseif ($dateTime instanceof DateTime) {
		return true;
	} elseif (is_numeric($dateTime)) {
		return true;
	} elseif (is_string($dateTime)) {
		throw new NotImplementedException();
	} else {
		throw new NotImplementedException();
	}
}

function allConditionsWithEarlyExitButWithoutElse($dateTime) {
	if ($dateTime instanceof DateTimeImmutable) {
		return true;
	} elseif ($dateTime instanceof DateTime) {
		return true;
	} elseif (is_numeric($dateTime)) {
		return true;
	} elseif (is_string($dateTime)) {
		throw new NotImplementedException();
	}
}

function ifElseInElse() {
	if (true) {
		doSomething();
	} else {
		if (true) {
			return;
		} elseif (false) {
			return;
		} else {
			return;
		}
	}
}

function logicalCombinedComplicatedCondition() {
	foreach ($records as $record) {
		if (in_array($record['type'], ['A', 'C']) && (strpos($record['name'], 'www.') === 0 || strpos($record['name'], 'ftp.') === 0)) {
			echo 'You should see this echo twice!'.PHP_EOL;
		}
	}
}

function logicalVeryComplicatedCondition() {
	if ((true) && ((false || true) || (false && (true)))) {
		doSomething();
	}
}

function negativeInstanceOf($phpEditor, $jsonEditor) {
	$this->phpEditor = $phpEditor;
	$this->jsonEditor = $jsonEditor;

	if ($this->phpEditor instanceof CallbackReceiver) {
		$this->phpEditor->setCallback($this->getCallback());
	}
	if (!($this->jsonEditor instanceof CallbackReceiver)) {
		doSomething();
	}
}

function uselessElse() {
	if (true) {
		return;
	} elseif (false) {
		return;
	} else {
		echo 'Please do not use else!';
	}
}

function uselessElseIf() {
	if (true) {
		return true;
	} elseif (false) {
		doSomething();
	} else {
		doSomethingElse();
	}
}

function heredoc() {
	foreach ([] as $f) {
		if (file_exists($f)) {
			echo <<<EOF
XYZ
EOF;
		}
	}
}

function nowdoc() {
	foreach ([] as $f) {
		if (file_exists($f)) {
			echo <<<'EOF'
XYZ
EOF;
		}
	}
}

function uselessElseWithHeredoc() {
	if (true) {
		return;
	} elseif (false) {
		return <<<EOF
		XYZ
EOF;
	} else {
		echo <<<EOF
	XYZ
EOF;
	}
}

function inlineCommentAfterForeach()
{
	foreach ([] as $_) { // Comment
		if (true) {
			$x = 1;
		}
	}
}

function moreInlineComments()
{
	if (true) { // Comment
		return 1;
	} elseif (true) { // Comment
		return 2;
	} else { // Comment
		return 3;
	}
}

class Whatever
{
	public function moreInlineCommentsWithMoreIndentation(): int
	{
		if (true) { // Comment
			return 1;
		} elseif (true) { // Comment
			return 2;
		} else { // Comment
			return 3;
		}
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
		} elseif ($b === 2) {
			throw new \Exception('2');
		}

		throw new \Exception('anything');
	}
};

function conditionWithNullCoalesceOperator()
{
	foreach ([] as $key => $item) {
		if ($key ?? false) {
			yield $item;
		}
	}
};

function conditionWithShortTernaryOperator()
{
	foreach ([] as $key => $item) {
		if ($key ?: false) {
			yield $item;
		}
	}
};

// Simple else - needs to be last
if (true) {
	return true;
} else {
	doSomething();
}
