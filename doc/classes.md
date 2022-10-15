## Classes

#### SlevomatCodingStandard.Classes.BackedEnumTypeSpacing 🔧

* Checks number of spaces before `:` and before type.

Sniff provides the following settings:

* `spacesCountBeforeColon`: the number of spaces before `:`.
* `spacesCountBeforeType`: the number of spaces before type.

#### SlevomatCodingStandard.Classes.ClassLength

Disallows long classes. This sniff provides the following settings:

* `includeComments`: should comments be included in the count (default value is false).
* `includeWhitespace`: should empty lines be included in the count (default value is false).
* `maxLinesLength`: specifies max allowed function lines length (default value is 250).

#### SlevomatCodingStandard.Classes.ClassConstantVisibility 🔧

In PHP 7.1+ it's possible to declare [visibility of class constants](https://wiki.php.net/rfc/class_const_visibility). In a similar vein to optional declaration of visibility for properties and methods which is actually required in sane coding standards, this sniff also requires declaring visibility for all class constants.

Sniff provides the following settings:

* `fixable`: the sniff is not fixable by default because we think it's better to decide about each constant one by one, however you can enable fixability with this option.

```php
const FOO = 1; // visibility missing!
public const BAR = 2; // correct
```

#### SlevomatCodingStandard.Classes.ClassMemberSpacing 🔧

Checks lines count between different class members, eg. between last property and first method.

Sniff provides the following settings:

* `linesCountBetweenMembers`: lines count between different class members

#### SlevomatCodingStandard.Classes.ClassStructure 🔧

Checks that class/trait/interface members are in the correct order.

Sniff provides the following settings:

* `groups`: order of groups. Use multiple groups in one `<element value="">` to not differentiate among them. You can use specific groups or shortcuts.

**List of supported groups**:
uses,
enum cases,
public constants, protected constants, private constants,
public properties, public static properties, protected properties, protected static properties, private properties, private static properties,
constructor, static constructors, destructor, magic methods,
public methods, protected methods, private methods,
public final methods, public static final methods, protected final methods, protected static final methods,
public abstract methods, public static abstract methods, protected abstract methods, protected static abstract methods,
public static methods, protected static methods, private static methods,
private methods

**List of supported shortcuts**:
constants, properties, static properties, methods, all public methods, all protected methods, all private methods, static methods, final methods, abstract methods

```xml
<rule ref="SlevomatCodingStandard.Classes.ClassStructure">
	<properties>
		<property name="groups" type="array">
			<element value="uses"/>

			<element value="enum cases"/>

			<!-- Public constants are first but you don't care about the order of protected or private constants -->
			<element value="public constants"/>
			<element value="constants"/>

			<!-- You don't care about the order among the properties. The same can be done with "properties" shortcut -->
			<element value="public properties, protected properties, private properties"/>

			<!-- Constructor is first, then all public methods, then protected/private methods and magic methods are last -->
			<element value="constructor"/>
			<element value="all public methods"/>
			<element value="methods"/>
			<element value="magic methods"/>
		</property>
	</properties>
</rule>
```

#### SlevomatCodingStandard.Classes.ConstantSpacing 🔧

Checks that there is a certain number of blank lines between constants.

Sniff provides the following settings:

* `minLinesCountBeforeWithComment`: minimum number of lines before constant with a documentation comment or attribute
* `maxLinesCountBeforeWithComment`: maximum number of lines before constant with a documentation comment or attribute
* `minLinesCountBeforeWithoutComment`: minimum number of lines before constant without a documentation comment or attribute
* `maxLinesCountBeforeWithoutComment`: maximum number of lines before constant without a documentation comment or attribute

#### SlevomatCodingStandard.Classes.DisallowConstructorPropertyPromotion

Disallows usage of constructor property promotion.

#### SlevomatCodingStandard.Classes.DisallowLateStaticBindingForConstants 🔧

Disallows late static binding for constants.

#### SlevomatCodingStandard.Classes.DisallowMultiConstantDefinition 🔧

Disallows multi constant definition.

#### SlevomatCodingStandard.Classes.DisallowMultiPropertyDefinition 🔧

Disallows multi property definition.

#### SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces 🔧

Enforces one configurable number of lines after opening class/interface/trait brace and one empty line before the closing brace.

Sniff provides the following settings:

* `linesCountAfterOpeningBrace`: allows to configure the number of lines after opening brace.
* `linesCountBeforeClosingBrace`: allows to configure the number of lines before closing brace.

#### SlevomatCodingStandard.Classes.ForbiddenPublicProperty

Disallows using public properties.

This sniff provides the following setting:

* `checkPromoted`: will check promoted properties too.

#### SlevomatCodingStandard.Classes.MethodSpacing 🔧

Checks that there is a certain number of blank lines between methods.

Sniff provides the following settings:

* `minLinesCount`: minimum number of blank lines
* `maxLinesCount`: maximum number of blank lines

#### SlevomatCodingStandard.Classes.ModernClassNameReference 🔧

Reports use of `__CLASS__`, `get_parent_class()`, `get_called_class()`, `get_class()` and `get_class($this)`.
Class names should be referenced via `::class` constant when possible.

Sniff provides the following settings:

* `enableOnObjects`: Enable `::class` on all objects. It's on by default if you're on PHP 8.0+

#### SlevomatCodingStandard.Classes.ParentCallSpacing 🔧

Enforces configurable number of lines around parent method call.

Sniff provides the following settings:

* `linesCountBefore`: allows to configure the number of lines before parent call.
* `linesCountBeforeFirst`: allows to configure the number of lines before first parent call.
* `linesCountAfter`: allows to configure the number of lines after parent call.
* `linesCountAfterLast`: allows to configure the number of lines after last parent call.

#### SlevomatCodingStandard.Classes.PropertyDeclaration 🔧

* Checks that there's a single space between a typehint and a property name: `Foo $foo`
* Checks that there's no whitespace between a nullability symbol and a typehint: `?Foo`
* Checks that there's a single space before nullability symbol or a typehint: `private ?Foo` or `private Foo`
* Checks order of modifiers

Sniff provides the following settings:

* `modifiersOrder`: allows to configure order of modifiers.
* `checkPromoted`: will check promoted properties too.
* `enableMultipleSpacesBetweenModifiersCheck`: checks multiple spaces between modifiers.

#### SlevomatCodingStandard.Classes.PropertySpacing 🔧

Checks that there is a certain number of blank lines between properties.

Sniff provides the following settings:

* `minLinesCountBeforeWithComment`: minimum number of lines before property with a documentation comment or attribute
* `maxLinesCountBeforeWithComment`: maximum number of lines before property with a documentation comment or attribute
* `minLinesCountBeforeWithoutComment`: minimum number of lines before property without a documentation comment or attribute
* `maxLinesCountBeforeWithoutComment`: maximum number of lines before property without a documentation comment or attribute

#### SlevomatCodingStandard.Classes.RequireAbstractOrFinal 🔧

Requires the class to be declared either as abstract or as final.

#### SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion 🔧

Requires use of constructor property promotion.

This sniff provides the following setting:

* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 8.0 or higher.

#### SlevomatCodingStandard.Classes.RequireMultiLineMethodSignature 🔧

Enforces method signature to be splitted to more lines so each parameter is on its own line.

Sniff provides the following settings:

* `minLineLength`: specifies min line length to enforce signature to be splitted. Use 0 value to enforce for all methods, regardless of length.

* `includedMethodPatterns`: allows to configure which methods are included in sniff detection. This is an array of regular expressions (PCRE) with delimiters. You should not use this with `excludedMethodPatterns`, as it will not work properly.

* `excludedMethodPatterns`: allows to configure which methods are excluded from sniff detection. This is an array of regular expressions (PCRE) with delimiters. You should not use this with `includedMethodPatterns`, as it will not work properly.

#### SlevomatCodingStandard.Classes.RequireSingleLineMethodSignature 🔧

Enforces method signature to be on a single line.

Sniff provides the following settings:

* `maxLineLength`: specifies max allowed line length. If signature would fit on it, it's enforced. Use 0 value to enforce for all methods, regardless of length.

* `includedMethodPatterns`: allows to configure which methods are included in sniff detection. This is an array of regular expressions (PCRE) with delimiters. You should not use this with `excludedMethodPatterns`, as it will not work properly.

* `excludedMethodPatterns`: allows to configure which methods are excluded from sniff detection. This is an array of regular expressions (PCRE) with delimiters. You should not use this with `includedMethodPatterns`, as it will not work properly.

#### SlevomatCodingStandard.Classes.SuperfluousAbstractClassNaming

Reports use of superfluous prefix or suffix "Abstract" for abstract classes.

#### SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming

Reports use of superfluous prefix or suffix "Interface" for interfaces.

#### SlevomatCodingStandard.Classes.SuperfluousExceptionNaming

Reports use of superfluous suffix "Exception" for exceptions.

#### SlevomatCodingStandard.Classes.SuperfluousErrorNaming

Reports use of superfluous suffix "Error" for errors.

#### SlevomatCodingStandard.Classes.SuperfluousTraitNaming

Reports use of superfluous suffix "Trait" for traits.

#### SlevomatCodingStandard.Classes.TraitUseDeclaration 🔧

Prohibits multiple traits separated by commas in one `use` statement.

#### SlevomatCodingStandard.Classes.TraitUseSpacing 🔧

Enforces configurable number of lines before first `use`, after last `use` and between two `use` statements.

Sniff provides the following settings:

* `linesCountBeforeFirstUse`: allows to configure the number of lines before first `use`.
* `linesCountBeforeFirstUseWhenFirstInClass`: allows to configure the number of lines before first `use` when the `use` is the first statement in the class.
* `linesCountBetweenUses`: allows to configure the number of lines between two `use` statements.
* `linesCountAfterLastUse`: allows to configure the number of lines after last `use`.
* `linesCountAfterLastUseWhenLastInClass`: allows to configure the number of lines after last `use` when the `use` is the last statement in the class.

#### SlevomatCodingStandard.Classes.UselessLateStaticBinding 🔧

Reports useless late static binding.
