<?php

/**
 * Test that our token stack is properly maintained with the "missing" break statement
 */
function caseWithoutBreak($string, $thing)
{
    switch ($string) {
        case 'foo':
            switch ($thing) {
                case 'ding':
                case 'dong':
                    $string = 'dang';
                    // no break here!!
            }
            break;
        case 'bar':
        case 'baz':
        case 'biz':
            switch ($thing) {
                case 'ding':
                case 'dong':
                    $string = 'dang';
                    break;
            }
            break;
    }
}
