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
				'',
			],
			[
				$root . '/unknown',
				null,
				['php'],
				$root,
			],
			[
				$root . '/app',
				null,
				['php'],
				$root,
			],
			[
				$root . '/app/components',
				null,
				['php'],
				$root,
			],
			[
				$root . '/app/components.php',
				'Slevomat\\components',
				['php'],
				$root,
			],
			[
				$root . '/app/Foo/Foo.swift',
				null,
				['php'],
				$root,
			],
			[
				$root . '/app/Foo/Foo.php',
				'Slevomat\\Foo\\Foo',
				['php'],
				$root,
			],
			[
				$root . '/tests/UnitTestCase.php',
				'Slevomat\\UnitTestCase',
				['php'],
				$root,
			],
			[
				$root . '/app/model/Auth/DatabaseAuthenticator.php',
				'Slevomat\\Auth\\DatabaseAuthenticator',
				['php'],
				$root,
			],
			[
				$root . '/app/services/Delivery/Premise/AbstractPremiseImporter.php',
				'Slevomat\\Delivery\\Premise\\AbstractPremiseImporter',
				['php'],
				$root,
			],
			[
				$root . '/app/ui/Admin/Newsletter/CreatePresenter.php',
				'Slevomat\\UI\\Admin\\Newsletter\\CreatePresenter',
				['php'],
				$root,
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.php',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				['php'],
				$root,
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.PHP',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				['php'],
				$root,
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.php',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				['PHP'],
				$root,
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.php',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				['php', 'lsp'],
				$root,
			],
			[
				$root . '/app/Foo/FooTest.phpt',
				'Slevomat\\Foo\\FooTest',
				['php', 'phpt'],
				$root,
			],
			[
				'/www/src/src/Foo/FooTest.php',
				'Slevomat\\Foo\\FooTest',
				['php', 'phpt'],
				'/www/src',
			],
		];
	}

	/**
	 * @dataProvider dataGetTypeNameFromProjectPath
	 * @param string $path
	 * @param string|null $expectedNamespace
	 * @param string[] $extensions
	 * @param string $currentDir
	 */
	public function testGetTypeNameFromProjectPath(string $path, ?string $expectedNamespace, array $extensions, string $currentDir): void
	{
		$extractor = new FilepathNamespaceExtractorStub(
			[
				'app/ui' => 'Slevomat\\UI',
				'app' => 'Slevomat',
				'tests' => 'Slevomat',
				'src' => 'Slevomat',
			],
			['components', 'forms', 'model', 'models', 'services', 'stubs', 'data'],
			$extensions,
			$currentDir
		);
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		self::assertSame($expectedNamespace, $extractor->getTypeNameFromProjectPath($path));
	}

}
