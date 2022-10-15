## Files

#### SlevomatCodingStandard.Files.FileLength

Disallows long files. This sniff provides the following settings:

* `includeComments`: should comments be included in the count (default value is false).
* `includeWhitespace`: should empty lines be included in the count (default value is false).
* `maxLinesLength`: specifies max allowed function lines length (default value is 250).

#### SlevomatCodingStandard.Files.LineLength

Enforces maximum length of a single line of code.

Sniff provides the following settings:

* `lineLengthLimit`: actual limit of the line length
* `ignoreComments`: whether or not to ignore line length of comments
* `ignoreImports`: whether or not to ignore line length of import (use) statements

#### SlevomatCodingStandard.Files.TypeNameMatchesFileName

For projects not following the [PSR-0](http://www.php-fig.org/psr/psr-0/) or [PSR-4](http://www.php-fig.org/psr/psr-4/) autoloading standards, this sniff checks whether a namespace and a name of a class/interface/trait follows agreed-on way to organize code into directories and files.

Other than enforcing that the type name must match the name of the file it's contained in, this sniff is very configurable. Consider the following sample configuration:

```xml
<rule ref="SlevomatCodingStandard.Files.TypeNameMatchesFileName">
	<properties>
		<property name="rootNamespaces" type="array">
			<element key="app/ui" value="Slevomat\UI"/>
			<element key="app" value="Slevomat"/>
			<element key="build/SlevomatSniffs/Sniffs" value="SlevomatSniffs\Sniffs"/>
			<element key="tests/ui" value="Slevomat\UI"/>
			<element key="tests" value="Slevomat"/>
		</property>
		<property name="skipDirs" type="array">
			<element value="components"/>
			<element value="forms"/>
			<element value="model"/>
			<element value="models"/>
			<element value="services"/>
			<element value="stubs"/>
			<element value="data"/>
			<element value="new"/>
		</property>
		<property name="ignoredNamespaces" type="array">
			<element value="Slevomat\Services"/>
		</property>
	</properties>
</rule>
```

Sniff provides the following settings:

* `rootNamespaces` property expects configuration similar to PSR-4 - project directories mapped to certain namespaces.
* `skipDirs` are not taken into consideration when comparing a path to a namespace. For example, with the above settings, file at path `app/services/Product/Product.php` is expected to contain `Slevomat\Product\Product`, not `Slevomat\services\Product\Product`.
* `extensions`: allow different file extensions. Default is `php`.
* `ignoredNamespaces`: sniff is not performed on these namespaces.
