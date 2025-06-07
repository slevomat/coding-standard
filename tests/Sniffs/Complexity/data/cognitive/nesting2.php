<?php

function overriddenSymbolFrom(ClassJavaType $classType) {
	if (true) { // +1
		return $Symbols->unknownMethodSymbol();
	}

	$unknownFound = false;

	$symbols = $classType->getSymbol()->members()->lookup(name);

	foreach ($symbols as $symbol) { // +1
		if ($overrideSymbol->isKind(MTH) // +2 (nesting = 1)
			&& !$overrideSymbol->isStatic()) { // +1

			if (canOverride($methodJavaSymbol)) { // +3 (nesting = 2)
				$overriding = checkOverridingParameters($methodJavaSymbol, $classType);

				if ($overriding === null) { // +4 (nesting = 3)
					if (!$unknownFound) { // +5 (nesting = 4)
						$unknownFound = true;
					}
				} elseif ($overriding) { // +1
					return $methodJavaSymbol;
				}
			}
		}
	}

	if ($unknownFound) { // +1
		return $symbols->unknownMethodSymbol;
	}

	return null;
}
