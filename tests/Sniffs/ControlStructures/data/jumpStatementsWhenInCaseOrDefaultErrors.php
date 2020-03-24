<?php

function () {
	switch (true) {
		case 1:

			throw new Exception();
		case 2:

			if (false) {
				return false;
			}

			return;

		case 3:
			break;
		case 4:
			echo 1;

		default:
			break;

	}
};
