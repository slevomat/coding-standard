<?php

(new Foo())->__invoke();

Closure::bind(function ($instance, string $propertyName, $value) : void {
	$instance->{$propertyName} = $value;
}, $instance, null)->__invoke($instance, $this->getName(), $value);

$anything->__invoke(null, false, true);

$this->callback->__invoke();

$this->foo->bar->__invoke(null);

self::$callback->__invoke();

$this->{$name}->__invoke();

$this->callbacks['callback']->__invoke();

$this->getFoo()->callback->__invoke();

$this->getFoo()->$callback->__invoke();
