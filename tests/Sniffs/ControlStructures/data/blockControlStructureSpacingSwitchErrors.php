<?php

switch ($condition)
{

	case 'a':

		return new A();


	case 'b':

		return new B();
	default:

		throw new InvalidArgumentException('...');

}

switch (true) {
	default:
	case 1:
		echo 2;
}

switch (true) {
	case 1:
	default:
		echo 2;
}

switch ($foo) {
    case 1:
        return 1;
        // comment and two following empty lines


    case 2:
        return 2;
}
