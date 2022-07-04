<?php

function testHybrid($foo, $bar)
{
    if ($foo === 'a') {  // +1
        return;
    } elseif ($foo === 'b') { // +1
        if ($bar === 'a') { // +2
            if (\rand() === 0.5) { // +3
                return;
            }
        } elseif ($bar === 'b') {   // +1  (does not receive nesting increment)
            return;
        }
    }
}
