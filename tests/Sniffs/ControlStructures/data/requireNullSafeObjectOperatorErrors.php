<?php // lint >= 8.0

$a = $b !== null ? $b->getSomething() : null;
$a = null !== $b ? $b->getSomething() : null;

$a = $b === null ? null : $b->getSomething();
$a = null === $b ? null : $b->getSomething();

$a = $b !== null ? $b->getSomething() : 'default';
$a = null !== $b ? $b->getSomething() : 'default';

$d = $a !== null && $a->getB() !== null && $a->getB()->getC() !== null ? $a->getB()->getC()->getD() : null;
$d = $a === null || $a->getB() === null || $a->getB()->getC() === null ? null : $a->getB()->getC()->getD();

$e = $a !== null && $b->getC() !== null && $b->getC()->getD() !== null ? true : false;

function ($product) {
	$limitedCategories = $product->getProductCashback() !== null
		? $product->getProductCashback()->getLimitedCategories()->map(static fn (ProductCategory $category): int => $category->getId())->getValues()
		: [];

	return $limitedCategories;
};
