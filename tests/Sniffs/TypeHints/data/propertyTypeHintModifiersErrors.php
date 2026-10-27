<?php // lint >= 8.4

final class Whatever
{

	public array $firstPropertyInFinalClass;

	public readonly array $readonlyWithoutItemsType;

	/** @var int */
	public readonly int $readonlyWithUselessAnnotation;

	public private(set) array $asymmetricWithoutItemsType;

	private(set) array $implicitPublicAsymmetricWithoutItemsType;

}

$anonymous = new readonly class {

	public array $firstPropertyInReadonlyAnonymousClass;

};
