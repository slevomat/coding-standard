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

function ($gatewayData, $response) {
	$gatewayData->setResponseData(
		$response->getPaymentStatus()?->getValue(),
		$response->getResultCode()->getValue(),
		$this->getSerializedResponse($response),
	);
};

// Must be last
if ($b->getC()?->getD() !== null) {

}
