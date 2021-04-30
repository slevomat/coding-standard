<?php

interface methodPerInterfaceLimitErrors
{

	public function voidMethod(): void;

	public function boolMethod(): bool;

	public function intMethod(): int;

	public function floatMethod(): float;

	public function stringMethod(): string;

	public function arrayMethod(...$values): array;

	function anonymousClassMethod();

	function selfMethod(): self;

	function untypedMethod($x);

	function finalAllowedMethod();

	public function oneTooManyMethod(): string;

}
