<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

class FilepathNamespaceExtractorTest extends \PHPUnit_Framework_TestCase
{

	public function dataGetTypeNameFromProjectPath(): array
	{
		$root = '/Users/www/slevomat';

		return [
			[
				$root . '/unknown',
				null,
			],
			[
				$root . '/app',
				null,
			],
			[
				$root . '/app/components',
				null,
			],
			[
				$root . '/app/components.php',
				'Slevomat\\components',
			],
			[
				$root . '/app/Foo/Foo.swift',
				null,
			],
			[
				$root . '/app/Foo/Foo.php',
				'Slevomat\\Foo\\Foo',
			],
			[
				$root . '/tests/UnitTestCase.php',
				'Slevomat\\UnitTestCase',
			],
			[
				$root . '/app/model/Auth/DatabaseAuthenticator.php',
				'Slevomat\\Auth\\DatabaseAuthenticator',
			],
			[
				$root . '/app/services/Delivery/Premise/AbstractPremiseImporter.php',
				'Slevomat\\Delivery\\Premise\\AbstractPremiseImporter',
			],
			[
				$root . '/app/ui/Admin/Newsletter/CreatePresenter.php',
				'Slevomat\\UI\\Admin\\Newsletter\\CreatePresenter',
			],
			[
				$root . '/tests/services/Delivery/Goods3rd/data/StatusEnum.php',
				'Slevomat\\Delivery\\Goods3rd\\StatusEnum',
			],
		];
	}

	/**
	 * @dataProvider dataGetTypeNameFromProjectPath
	 * @param string $path
	 * @param string|null $expectedNamespace
	 */
	public function testGetTypeNameFromProjectPath(string $path, string $expectedNamespace = null)
	{
		$extractor = new FilepathNamespaceExtractor(
			[
				'app/ui' => 'Slevomat\\UI',
				'app' => 'Slevomat',
				'tests' => 'Slevomat',
			],
			['components', 'forms', 'model', 'models', 'services', 'stubs', 'data']
		);
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		$this->assertSame($expectedNamespace, $extractor->getTypeNameFromProjectPath($path));
	}

}
