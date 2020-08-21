<?php

// Comment
if (true) {

}

/* Comment */
do {

} while (true);

/*
 Comment
 */
while (true) {

}

// phpcs:disable
function () {

};
// phpcs:enable

/**
 * phpcs:disable SomeSniff
 */
class Whatever
{

}

$match = Strings::match($address, /** @lang RegExp */'~^regexp$~');
