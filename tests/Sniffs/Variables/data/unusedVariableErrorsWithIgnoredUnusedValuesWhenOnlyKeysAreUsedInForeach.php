<?php

$used = false;

foreach ([] as $key => $value) {
	echo $key;
}

$used = true;
while ($used) {

}

foreach (range(0, 5) as $otherKey => $otherValue) {
	echo $otherKey;
}

foreach (range(0, 5) as $someKey => $someValue) {
	// Key and value are not used
}
