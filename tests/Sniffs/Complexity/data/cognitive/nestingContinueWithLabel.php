<?php

function sumOfPrimes(int $max): int {
    $total = 0;
    for ($i = 1; $i <= $max; ++$i) { // +1
        for ($j = 2; $j < $i; ++$j) { // +2
            if ($i % $j == 0) { // +3
                continue 2; // +1
            }
        }
        $total += $i;
    }

    return $total;
}
