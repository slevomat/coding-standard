<?php

(new Foo())->__invoke();

Closure::bind(function ($instance, string $propertyName, $value) : void {
    $instance->{$propertyName} = $value;
}, $instance, null)->__invoke($instance, $this->getName(), $value);

$anything->__invoke(null, false, true);
