## Attributes

#### SlevomatCodingStandard.Attributes.AttributeAndTargetSpacing 🔧

Sniff checks lines count between attribute and its target (or target's documentation comment).

Sniff provides the following settings:

* `allowOnSameLine` (default: `false`): allow attribute and its target to be placed on the same line
* `linesCount`: lines count between attribute and its target

#### SlevomatCodingStandard.Attributes.AttributesOrder 🔧

Requires order of attributes. When more attributes are in one `#[]`, e.g. `#[One, Two]`, the first attribute name is used to resolve the order.

Sniff provides the following settings:

* `order`: required order of attributes. Supports prefixes, eg. `ORM\`, and mask , eg. `AppAssert*`.
* `orderAlphabetically`: order attributes alphabetically. Boolean value, default `false`.

Only one order can be set.

```xml
<rule ref="SlevomatCodingStandard.Attributes.AttributesOrder">
	<properties>
		<property name="order" type="array">
			<element value="ORM\Table"/>
			<element value="ORM\"/>
			<element value="One"/>
			<element value="Two"/>
		</property>
	</properties>
</rule>
```

#### SlevomatCodingStandard.Attributes.DisallowAttributesJoining 🔧

Requires that only one attribute can be placed inside `#[]` (no comma-separated list). In case of more attributes applied, they are split into individual `#[]` blocks.

#### SlevomatCodingStandard.Attributes.DisallowMultipleAttributesPerLine 🔧

Disallows multiple attributes of some target on same line.
This sniff treats multiple attributes declared inside one `#[]` as a single attribute. See `DisallowAttributesJoining` to modify this behavior.

#### SlevomatCodingStandard.Attributes.RequireAttributeAfterDocComment 🔧

Requires that attributes are always after documentation comment.

