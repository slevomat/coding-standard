<?php

function myMethod2 () {
    $r = function() { // +0 (but nesting level is now 1)
        if (true) { // +2 (nesting=1)
        }
    };
}
