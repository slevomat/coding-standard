<?php /** @var Token $token */ ?>
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
	public function doAnything()
	{
		/*
		 Comment
		 */
		while (true) {

		}
	}
}

$match = Strings::match($address, /** @lang RegExp */'~^regexp$~');

/** phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps */
class Something
{

}
