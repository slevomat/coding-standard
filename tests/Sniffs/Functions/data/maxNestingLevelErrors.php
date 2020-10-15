<?php

function dummyFunctionWithFourLevelNesting()
{
	if ($condition) {
		echo 'hi';

		switch ($condition) {
			case '1':
				if ($condition === '1') {
					if ($cond) {
						echo 'hi';
					}
				}

				break;
		}
	}
}
