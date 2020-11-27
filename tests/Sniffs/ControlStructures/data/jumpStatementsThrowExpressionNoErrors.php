<?php // lint >= 8.0

$callable = fn() => throw new Exception();

$value = $nullableValue ?? throw new InvalidArgumentException();

$value = $falsableValue ?: throw new InvalidArgumentException();

$value = !empty($array)
    ? reset($array)
    : throw new InvalidArgumentException();

$condition && throw new Exception();
$condition || throw new Exception();
$condition and throw new Exception();
$condition or throw new Exception();
