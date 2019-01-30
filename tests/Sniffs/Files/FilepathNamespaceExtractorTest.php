<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

use SlevomatCodingStandard\Sniffs\TestCase;
use function str_replace;
use const DIRECTORY_SEPARATOR;

class FilepathNamespaceExtractorTest extends TestCase
{

	/**
	 * @return mixed[][]
	 */
	public function dataGetTypeNameFromProjectPath(): array
	{
		$root = '/Users/www/slevomat';

		return [
			[
				'unknown.php',
				null,
				['php'],
			],
			[
				$root . '/unknown',
				null,
				['php'],
			],
			[
				$root . '/app',
				null,
				['php'],
			],
			[
				$root . '/app/components',
				null,
				['php'],
			],
			[
				$root . '/app/components.php',
				'Slevomat\\components',
				['php'],
			],
			[
				$root . '/app/Foo/Foo.swift',
				null,
				['php'],
			],
			[
				$root . '/app/Foo/Foo.php',
				'Slevomat\\Foo\\Foo',
				['php'],
			],
			[
				$root . '/tests/UnitTestCase.php',
				'Slevomat\\UnitTestCase',
				['php'],
			],
			[
				$root . '/app/model/Auth/DatabaseAuthenticator.php',
				'Slevomat\\Auth\\DatabaseAuthenticator',
				['php'],
			],
			[
				$root . '/app/services/Delivery/Premise/AbstractPremiseImporter.php',
				'Slevomat\\Delivery\\Premise\\AbstractPremiseImporter',
				['php'],
			],
			[
				$root . '/app/ui/Admin/Newsletter/CreatePresenter.php',
				'Slevomat\\UI\\Admin\\Newsletter\\CreatePresenter',
				['php'],
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.php',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				['php'],
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.PHP',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				['php'],
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.php',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				['PHP'],
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.php',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				['php', 'lsp'],
			],
			[
				$root . '/app/Foo/FooTest.phpt',
				'Slevomat\\Foo\\FooTest',
				['php', 'phpt'],
			],
		];
	}

	/**
	 * @dataProvider dataGetTypeNameFromProjectPath
	 * @param string $path
	 * @param string|null $expectedNamespace
	 * @param string[] $extensions
	 */
	public function testGetTypeNameFromProjectPath(string $path, ?string $expectedNamespace, array $extensions): void
	{
		$extractor = new FilepathNamespaceExtractor(
			[
				'app/ui' => 'Slevomat\\UI',
				'app' => 'Slevomat',
				'tests' => 'Slevomat',
			],
			['components', 'forms', 'model', 'models', 'services', 'stubs', 'data'],
			$extensions
		);
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		self::assertSame($expectedNamespace, $extractor->getTypeNameFromProjectPath($path));
	}

}
