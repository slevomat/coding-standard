## Commenting

#### SlevomatCodingStandard.Commenting.AnnotationName 🔧

Reports incorrect annotation name. It reports standard annotation names used by phpDocumentor, PHPUnit, PHPStan and Psalm by default.
Unknown annotation names are ignored.

Sniff provides the following settings:

* `annotations`: allows to configure which annotations are checked and how.

#### SlevomatCodingStandard.Commenting.DeprecatedAnnotationDeclaration

Reports `@deprecated` annotations without description.

#### SlevomatCodingStandard.Commenting.DisallowCommentAfterCode 🔧

Sniff disallows comments after code at the same line.

#### SlevomatCodingStandard.Commenting.ForbiddenAnnotations 🔧

Reports forbidden annotations. No annotations are forbidden by default, the configuration is completely up to the user. It's recommended to forbid obsolete and inappropriate annotations like:

* `@author`, `@created`, `@version`: we have version control systems.
* `@package`: we have namespaces.
* `@copyright`, `@license`: it's not necessary to repeat licensing information in each file.
* `@throws`: it's not possible to enforce this annotation and the information can become outdated.

Sniff provides the following settings:

* `forbiddenAnnotations`: allows to configure which annotations are forbidden to be used.

#### SlevomatCodingStandard.Commenting.ForbiddenComments 🔧

Reports forbidden comments in descriptions. Nothing is forbidden by default, the configuration is completely up to the user. It's recommended to forbid generated or inappropriate messages like:

* `Constructor.`
* `Created by PhpStorm.`

Sniff provides the following settings:

* `forbiddenCommentPatterns`: allows to configure which comments are forbidden to be used. This is an array of regular expressions (PCRE) with delimiters.

#### SlevomatCodingStandard.Commenting.DocCommentSpacing 🔧

Enforces configurable number of lines before first content (description or annotation), after last content (description or annotation),
between description and annotations, between two different annotation types (eg. between `@param` and `@return`).

Sniff provides the following settings:

* `linesCountBeforeFirstContent`: allows to configure the number of lines before first content (description or annotation).
* `linesCountBetweenDescriptionAndAnnotations`: allows to configure the number of lines between description and annotations.
* `linesCountBetweenDifferentAnnotationsTypes`: allows to configure the number of lines between two different annotation types.
* `linesCountBetweenAnnotationsGroups`: allows to configure the number of lines between annotation groups.
* `linesCountAfterLastContent`: allows to configure the number of lines after last content (description or annotation).
* `annotationsGroups`: allows to configure order of annotation groups and even order of annotations in every group. Supports prefixes, eg. `@ORM\`.

```xml
<rule ref="SlevomatCodingStandard.Commenting.DocCommentSpacing">
	<properties>
		<property name="annotationsGroups" type="array">
			<element value="
				@ORM\,
			"/>
			<element value="
				@var,
				@param,
				@return,
			"/>
		</property>
	</properties>
</rule>
```

If `annotationsGroups` is set, `linesCountBetweenDifferentAnnotationsTypes` is ignored and `linesCountBetweenAnnotationsGroups` is applied.
If `annotationsGroups` is not set, `linesCountBetweenAnnotationsGroups` is ignored and `linesCountBetweenDifferentAnnotationsTypes` is applied.

Annotations not in any group are placed to automatically created last group.

#### SlevomatCodingStandard.Commenting.EmptyComment 🔧

Reports empty comments.

#### SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration 🔧

Reports invalid inline phpDocs with `@var`.

Sniff provides the following settings:

* `allowDocCommentAboveReturn`: Allows documentation comments without variable name above `return` statement.
* `allowAboveNonAssignment`: Allows documentation comments above non-assignment if the line contains the right variable name.

#### SlevomatCodingStandard.Commenting.RequireOneLinePropertyDocComment 🔧

Requires property comments with single-line content to be written as one-liners.

#### SlevomatCodingStandard.Commenting.RequireOneLineDocComment 🔧

Sniff requires comments with single-line content to be written as one-liners.

#### SlevomatCodingStandard.Commenting.DisallowOneLinePropertyDocComment 🔧

Sniff requires comments with single-line content to be written as multi-liners.

#### SlevomatCodingStandard.Commenting.UselessFunctionDocComment 🔧

* Checks for useless doc comments. If the native method declaration contains everything and the phpDoc does not add anything useful, it's reported as useless and can optionally be automatically removed with `phpcbf`.
* Some phpDocs might still be useful even if they do not add any typehint information. They can contain textual descriptions of code elements and also some meaningful annotations like `@expectException` or `@dataProvider`.

Sniff provides the following settings:

* `traversableTypeHints`: enforces which typehints must have specified contained type. E.g. if you set this to `\Doctrine\Common\Collections\Collection`, then `\Doctrine\Common\Collections\Collection` must always be supplied with the contained type: `\Doctrine\Common\Collections\Collection|Foo[]`.

This sniff can cause an error if you're overriding or implementing a parent method which does not have typehints. In such cases add `@phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint` annotation to the method to have this sniff skip it.

#### SlevomatCodingStandard.Commenting.UselessInheritDocComment 🔧

Reports documentation comments containing only `{@inheritDoc}` annotation because inheritance is automatic, and it's not needed to use a special annotation for it.
