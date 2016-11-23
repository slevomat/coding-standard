<?php

namespace SlevomatCodingStandard\Sniffs\Files;

class FilepathNamespaceExtractorTest extends \PHPUnit_Framework_TestCase
{

	public function dataGetTypeNameFromProjectPath()
	{
		$root = '/Users/www/slevomat';

		return [
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
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.php',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				[],
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.php',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
				['php'],
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
			[
				$root . '/app/Foo/FooTest.phpt',
				null,
				['.php', '.phpt'],
			],
		];
	}

	/**
	 * @dataProvider dataGetTypeNameFromProjectPath
	 * @param string $path
	 * @param string $expectedNamespace
	 * @param array $allowedExtensions
	 */
	public function testGetTypeNameFromProjectPath($path, $expectedNamespace, $allowedExtensions)
	{
		$extractor = new FilepathNamespaceExtractor(
			[
				'app/ui' => 'Slevomat\\UI',
				'app' => 'Slevomat',
				'tests' => 'Slevomat',
			],
			['components', 'forms', 'model', 'models', 'services', 'stubs', 'data'],
			$allowedExtensions
		);
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		$this->assertSame($expectedNamespace, $extractor->getTypeNameFromProjectPath($path));
	}

}
