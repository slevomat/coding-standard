<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class NamespaceHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function dataIsFullyQualifiedName(): array
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
	public function testIsFullyQualifiedName(string $typeName)
	{
		$this->assertTrue(NamespaceHelper::isFullyQualifiedName($typeName));
	}

	public function dataIsNotFullyQualifiedName(): array
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
	public function testIsNotFullyQualifiedName(string $typeName)
	{
		$this->assertFalse(NamespaceHelper::isFullyQualifiedName($typeName));
	}

	public function dataHasNamespace(): array
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
	public function testHasNamespace(string $typeName)
	{
		$this->assertTrue(NamespaceHelper::hasNamespace($typeName));
	}

	public function dataDoesNotHaveNamespace(): array
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
	public function testDoesNotHaveNamespace(string $typeName)
	{
		$this->assertFalse(NamespaceHelper::hasNamespace($typeName));
	}

	public function dataGetNameParts(): array
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
	public function testGetNameParts(string $namespace, array $parts)
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

	public function dataGetUnqualifiedNameFromFullyQualifiedName(): array
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
	public function testGetUnqualifiedNameFromFullyQualifiedName(string $unqualifiedName, string $fullyQualifiedName)
	{
		$this->assertSame($unqualifiedName, NamespaceHelper::getUnqualifiedNameFromFullyQualifiedName($fullyQualifiedName));
	}

	public function dataIsQualifiedName(): array
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
	public function testIsQualifiedName(string $name)
	{
		$this->assertTrue(NamespaceHelper::isQualifiedName($name));
	}

	public function dataIsNotQualifiedName(): array
	{
		return [
			['Foo'],
		];
	}

	/**
	 * @dataProvider dataIsNotQualifiedName
	 * @param string $name
	 */
	public function testIsNotQualifiedName(string $name)
	{
		$this->assertFalse(NamespaceHelper::isQualifiedName($name));
	}

	public function dataNormalizeToCanonicalName(): array
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
	public function testNormalizeToCanonicalName(string $normalizedName, string $originalName)
	{
		$this->assertSame($normalizedName, NamespaceHelper::normalizeToCanonicalName($originalName));
	}

	public function dataTypeIsInNamespace(): array
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
	public function testTypeIsInNamespace(string $typeName, string $namespace)
	{
		$this->assertTrue(NamespaceHelper::isTypeInNamespace($typeName, $namespace));
	}

	public function dataTypeIsNotInNamespace(): array
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
	public function testTypeIsNotInNamespace(string $typeName, string $namespace)
	{
		$this->assertFalse(NamespaceHelper::isTypeInNamespace($typeName, $namespace));
	}

}
