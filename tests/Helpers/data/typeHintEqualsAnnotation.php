<?php // lint >= 8.0

/**
 * @return scalar
 */
function scalar(): int|bool|float|string
{

}

/**
 * @return Foo&Bar
 */
function unionIsNotIntersection(): Foo|Bar
{
}

/**
 * @return non-empty-lowercase-string
 */
function fooFunctionWithReturnAnnotationComplexString(): string
{

}

/**
 * @return non-empty-array|null
 */
function fooFunctionWithReturnAnnotationSimpleHyphenedIterable(): ?array
{

}
