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
