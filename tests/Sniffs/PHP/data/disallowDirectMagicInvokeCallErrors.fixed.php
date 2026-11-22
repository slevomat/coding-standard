<?php

(new Foo())();

Closure::bind(function ($instance, string $propertyName, $value) : void {
	$instance->{$propertyName} = $value;
}, $instance, null)($instance, $this->getName(), $value);

$anything(null, false, true);

($this->callback)();

($this->foo->bar)(null);

(self::$callback)();

($this->{$name})();

$this->callbacks['callback']();

$this->getFoo()->callback->__invoke();

$this->getFoo()->$callback->__invoke();
