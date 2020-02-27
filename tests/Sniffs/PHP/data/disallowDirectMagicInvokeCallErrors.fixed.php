<?php

(new Foo())();

Closure::bind(function ($instance, string $propertyName, $value) : void {
    $instance->{$propertyName} = $value;
}, $instance, null)($instance, $this->getName(), $value);

$anything(null, false, true);
