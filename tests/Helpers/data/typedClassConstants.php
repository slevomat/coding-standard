<?php // lint >= 8.3

class Whatever
{

	public const ErrorCodeInterface CODE = Code::X;

	const int|Union|Union2 UNION = 1;

	const ?\Foo\Nullable NULLABLE = null, ANOTHER = null;

	const (Intersection&Intersection2)|null DNF = null;

	const array ARR = [];

	const UNTYPED = 1;

}
