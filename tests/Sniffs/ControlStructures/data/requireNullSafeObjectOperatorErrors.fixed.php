<?php // lint >= 8.0

$a = $b?->getSomething();
$a = $b?->getSomething();

$a = $b?->getSomething();
$a = $b?->getSomething();

$a = $b?->getSomething() ?? 'default';
$a = $b?->getSomething() ?? 'default';

$d = $a?->getB()?->getC()?->getD();
$d = $a?->getB()?->getC()?->getD();

$e = $a !== null && $b->getC()?->getD() !== null ? true : false;

function ($product) {
	$limitedCategories = $product->getProductCashback()?->getLimitedCategories()->map(static fn (ProductCategory $category): int => $category->getId())->getValues() ?? [];

	return $limitedCategories;
};
