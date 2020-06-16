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
 *
 */
class Whatever
{

}

$match = Strings::match($address, /** @lang RegExp */'~^regexp$~');
