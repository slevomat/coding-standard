<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class NamespaceHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function dataIsFullyQualifiedName()
	{
		return [
			[
				'\Foo',
				'\Foo\Bar',
			],
		];
	}

	/**
	 * @dataProvider dataIsFullyQualifiedName
	 * @param string $typeName
	 */
	public function testIsFullyQualifiedName($typeName)
	{
		$this->assertTrue(NamespaceHelper::isFullyQualifiedName($typeName));
	}

	public function dataIsNotFullyQualifiedName()
	{
		return [
			[
				'Bar',
				'Foo\Bar',
			],
		];
	}

	/**
	 * @dataProvider dataIsNotFullyQualifiedName
	 * @param string $typeName
	 */
	public function testIsNotFullyQualifiedName($typeName)
	{
		$this->assertFalse(NamespaceHelper::isFullyQualifiedName($typeName));
	}

	public function dataHasNamespace()
	{
		return [
			[
				'\Foo\Bar',
				'Foo\Bar',
			],
		];
	}

	/**
	 * @dataProvider dataHasNamespace
	 * @param string $typeName
	 */
	public function testHasNamespace($typeName)
	{
		$this->assertTrue(NamespaceHelper::hasNamespace($typeName));

	}

	public function dataDoesNotHaveNamespace()
	{
		return [
			[
				'Foo',
				'\Foo',
			],
		];
	}

	/**
	 * @dataProvider dataDoesNotHaveNamespace
	 * @param string $typeName
	 */
	public function testDoesNotHaveNamespace($typeName)
	{
		$this->assertFalse(NamespaceHelper::hasNamespace($typeName));
	}

	public function dataGetNameParts()
	{
		return [
			[
				'\Foo',
				['Foo'],
			],
			[
				'Foo',
				['Foo'],
			],
			[
				'\Foo\Bar\Baz',
				['Foo', 'Bar', 'Baz'],
			],
			[
				'Foo\Bar\Baz',
				['Foo', 'Bar', 'Baz'],
			],
		];
	}

	/**
	 * @dataProvider dataGetNameParts
	 * @param string $namespace
	 * @param mixed[] $parts
	 */
	public function testGetNameParts($namespace, array $parts)
	{
		$this->assertSame($parts, NamespaceHelper::getNameParts($namespace));
	}

	public function testFindCurrentNamespaceName()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/namespacedFile.php'
		);
		$namespace = NamespaceHelper::findCurrentNamespaceName(
			$codeSnifferFile,
			TokenHelper::getLastTokenPointer($codeSnifferFile)
		);
		$this->assertSame('Foo\Bar', $namespace);
	}

	public function testFindCurrentNamespaceNameInFileWithoutNamespace()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/fileWithoutNamespace.php'
		);
		$this->assertNull(
			NamespaceHelper::findCurrentNamespaceName(
				$codeSnifferFile,
				TokenHelper::getLastTokenPointer($codeSnifferFile)
			)
		);
	}

	public function testClosestNamespaceNameWithMultipleNamespacesInFile()
	{
		$codeSnifferFile = $this->getCodeSnifferFile(
			__DIR__ . '/data/multipleNamespaces.php'
		);
		$namespace = NamespaceHelper::findCurrentNamespaceName(
			$codeSnifferFile,
			TokenHelper::getLastTokenPointer($codeSnifferFile)
		);
		$this->assertSame('Lorem\Ipsum', $namespace);
	}

	public function dataGetUnqualifiedNameFromFullyQualifiedName()
	{
		return [
			[
				'Foo',
				'\Foo',
			],
			[
				'Foo',
				'Foo',
			],
			[
				'Baz',
				'\Foo\Bar\Baz',
			],
			[
				'Baz',
				'Foo\Bar\Baz',
			],
		];
	}

	/**
	 * @dataProvider dataGetUnqualifiedNameFromFullyQualifiedName
	 * @param string $unqualifiedName
	 * @param string $fullyQualifiedName
	 */
	public function testGetUnqualifiedNameFromFullyQualifiedName($unqualifiedName, $fullyQualifiedName)
	{
		$this->assertSame($unqualifiedName, NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($fullyQualifiedName));
	}

	public function dataIsQualifiedName()
	{
		return [
			['\Foo'],
			['\Foo\Bar'],
			['Foo\Bar'],
		];
	}

	/**
	 * @dataProvider dataIsQualifiedName
	 * @param string $name
	 */
	public function testIsQualifiedName($name)
	{
		$this->assertTrue(NamespaceHelper::isQualifiedName($name));
	}

	public function dataIsNotQualifiedName()
	{
		return [
			['Foo'],
		];
	}

	/**
	 * @dataProvider dataIsNotQualifiedName
	 * @param string $name
	 */
	public function testIsNotQualifiedName($name)
	{
		$this->assertFalse(NamespaceHelper::isQualifiedName($name));
	}

	public function dataNormalizeToCanonicalName()
	{
		return [
			[
				'Foo',
				'Foo',
			],
			[
				'Foo',
				'\Foo',
			],
			[
				'Foo\Bar\Baz',
				'Foo\Bar\Baz',
			],
			[
				'Foo\Bar\Baz',
				'\Foo\Bar\Baz',
			],
		];
	}

	/**
	 * @dataProvider dataNormalizeToCanonicalName
	 * @param string $normalizedName
	 * @param string $originalName
	 */
	public function testNormalizeToCanonicalName($normalizedName, $originalName)
	{
		$this->assertSame($normalizedName, NamespaceHelper::normalizeToCanonicalName($originalName));
	}

	public function dataTypeIsInNamespace()
	{
		return [
			[
				'Foo\Bar',
				'Foo',
			],
			[
				'\Foo\Bar',
				'Foo',
			],
			[
				'Lorem\Ipsum\Dolor',
				'Lorem\Ipsum',
			],
			[
				'\Lorem\Ipsum\Dolor',
				'Lorem\Ipsum',
			],
		];
	}

	/**
	 * @dataProvider dataTypeIsInNamespace
	 * @param string $typeName
	 * @param string $namespace
	 */
	public function testTypeIsInNamespace($typeName, $namespace)
	{
		$this->assertTrue(NamespaceHelper::isTypeInNamespace($typeName, $namespace));
	}

	public function dataTypeIsNotInNamespace()
	{
		return [
			[
				'Foo\Bar',
				'Bar',
			],
			[
				'\Foo\Bar',
				'Bar',
			],
			[
				'Fooo\Bar',
				'Foo',
			],
			[
				'\Fooo\Bar',
				'Foo',
			],
			[
				'Lorem\Ipsum\DolorBar',
				'Lorem\Ipsum\Dolor',
			],
			[
				'\Lorem\Ipsum\DolorBar',
				'Lorem\Ipsum\Dolor',
			],
		];
	}

	/**
	 * @dataProvider dataTypeIsNotInNamespace
	 * @param string $typeName
	 * @param string $namespace
	 */
	public function testTypeIsNotInNamespace($typeName, $namespace)
	{
		$this->assertFalse(NamespaceHelper::isTypeInNamespace($typeName, $namespace));
	}

}
