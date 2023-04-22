<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\PHPStan;

use PHPStan\Testing\TypeInferenceTestCase;

class GetTokenDynamicReturnTypeExtensionTest extends TypeInferenceTestCase
{

	/**
	 * @return iterable<mixed>
	 */
	public static function dataFileAsserts(): iterable
	{
		yield from self::gatherAssertTypes(__DIR__ . '/data/get-token-extension.php');
	}

	/**
	 * @dataProvider dataFileAsserts
	 */
	public function testFileAsserts(
		string $assertType,
		string $file,
		...$args
	): void
	{
		$this->assertFileAsserts($assertType, $file, ...$args);
	}

	public static function getAdditionalConfigFiles(): array
	{
		return [__DIR__ . '/get-token-extension.neon'];
	}

}
