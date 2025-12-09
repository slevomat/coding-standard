# Slevomat Coding Standard

[![Latest version](https://img.shields.io/packagist/v/slevomat/coding-standard.svg?colorB=007EC6)](https://packagist.org/packages/slevomat/coding-standard)
[![Downloads](https://img.shields.io/packagist/dt/slevomat/coding-standard.svg?colorB=007EC6)](https://packagist.org/packages/slevomat/coding-standard)
[![Build status](https://github.com/slevomat/coding-standard/actions/workflows/build.yml/badge.svg?branch=master)](https://github.com/slevomat/coding-standard/actions?query=workflow%3ABuild+branch%3Amaster)
[![Code coverage](https://codecov.io/gh/slevomat/coding-standard/branch/master/graph/badge.svg)](https://codecov.io/gh/slevomat/coding-standard)
![PHPStan](https://img.shields.io/badge/style-level%207-brightgreen.svg?&label=phpstan)

Slevomat Coding Standard for [PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer) provides sniffs that fall into three categories:

* Functional - improving the safety and behaviour of code
* Cleaning - detecting dead code
* Formatting - rules for consistent code looks

## Table of contents

1. [Alphabetical list of sniffs](#alphabetical-list-of-sniffs)
2. [Installation](#installation)
3. [How to run the sniffs](#how-to-run-the-sniffs)
 - [Choose which sniffs to run](#choose-which-sniffs-to-run)
 - [Exclude sniffs you don't want to run](#exclude-sniffs-you-dont-want-to-run)
4. [Fixing errors automatically](#fixing-errors-automatically)
5. [Suppressing sniffs locally](#suppressing-sniffs-locally)
6. [Contributing](#contributing)

## Alphabetical list of sniffs

🔧 = [Automatic errors fixing](#fixing-errors-automatically)

🚧 = [Sniff check can be suppressed locally](#suppressing-sniffs-locally)

 - [SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys](doc/arrays.md#slevomatcodingstandardarrayalphabeticallysortedbykeys) 🔧
 - [SlevomatCodingStandard.Arrays.ArrayAccess](doc/arrays.md#slevomatcodingstandardarraysarrayaccess-) 🔧
 - [SlevomatCodingStandard.Arrays.DisallowImplicitArrayCreation](doc/arrays.md#slevomatcodingstandardarraysdisallowimplicitarraycreation)
 - [SlevomatCodingStandard.Arrays.DisallowPartiallyKeyed](doc/arrays.md#slevomatcodingstandardarraysdisallowpartiallykeyed) 🚧
 - [SlevomatCodingStandard.Arrays.MultiLineArrayEndBracketPlacement](doc/arrays.md#slevomatcodingstandardarraysmultilinearrayendbracketplacement-) 🔧
 - [SlevomatCodingStandard.Arrays.SingleLineArrayWhitespace](doc/arrays.md#slevomatcodingstandardarrayssinglelinearraywhitespace-) 🔧
 - [SlevomatCodingStandard.Arrays.TrailingArrayComma](doc/arrays.md#slevomatcodingstandardarraystrailingarraycomma-) 🔧
 - [SlevomatCodingStandard.Attributes.AttributeAndTargetSpacing](doc/attributes.md#slevomatcodingstandardattributesattributeandtargetspacing-) 🔧
 - [SlevomatCodingStandard.Attributes.AttributesOrder](doc/attributes.md#slevomatcodingstandardattributesattributesorder-) 🔧
 - [SlevomatCodingStandard.Attributes.DisallowAttributesJoining](doc/attributes.md#slevomatcodingstandardattributesdisallowattributesjoining-) 🔧
 - [SlevomatCodingStandard.Attributes.DisallowMultipleAttributesPerLine](doc/attributes.md#slevomatcodingstandardattributesdisallowmultipleattributesperline-) 🔧
 - [SlevomatCodingStandard.Attributes.RequireAttributeAfterDocComment](doc/attributes.md#slevomatcodingstandardattributesrequireattributeafterdoccomment-) 🔧
 - [SlevomatCodingStandard.Classes.BackedEnumTypeSpacing](doc/classes.md#slevomatcodingstandardclassesbackedenumtypespacing-) 🔧
 - [SlevomatCodingStandard.Classes.ClassConstantVisibility](doc/classes.md#slevomatcodingstandardclassesclassconstantvisibility-) 🔧
 - [SlevomatCodingStandard.Classes.ClassLength](doc/classes.md#slevomatcodingstandardclassesclasslength)
 - [SlevomatCodingStandard.Classes.ClassMemberSpacing](doc/classes.md#slevomatcodingstandardclassesclassmemberspacing-) 🔧
 - [SlevomatCodingStandard.Classes.ClassStructure](doc/classes.md#slevomatcodingstandardclassesclassstructure-) 🔧
 - [SlevomatCodingStandard.Classes.ConstantSpacing](doc/classes.md#slevomatcodingstandardclassesconstantspacing-) 🔧
 - [SlevomatCodingStandard.Classes.DisallowConstructorPropertyPromotion](doc/classes.md#slevomatcodingstandardclassesdisallowconstructorpropertypromotion)
 - [SlevomatCodingStandard.Classes.DisallowLateStaticBindingForConstants](doc/classes.md#slevomatcodingstandardclassesdisallowlatestaticbindingforconstants-) 🔧
 - [SlevomatCodingStandard.Classes.DisallowMultiConstantDefinition](doc/classes.md#slevomatcodingstandardclassesdisallowmulticonstantdefinition-) 🔧
 - [SlevomatCodingStandard.Classes.DisallowMultiPropertyDefinition](doc/classes.md#slevomatcodingstandardclassesdisallowmultipropertydefinition-) 🔧
 - [SlevomatCodingStandard.Classes.DisallowStringExpressionPropertyFetch](doc/classes.md#slevomatcodingstandardclassesdisallowstringexpressionpropertyfetch-) 🔧
 - [SlevomatCodingStandard.Classes.EmptyLinesAroundClassBraces](doc/classes.md#slevomatcodingstandardclassesemptylinesaroundclassbraces-) 🔧
 - [SlevomatCodingStandard.Classes.EnumCaseSpacing](doc/classes.md#slevomatcodingstandardclassesenumcasespacing-) 🔧
 - [SlevomatCodingStandard.Classes.ClassKeywordOrder](doc/classes.md#slevomatcodingstandardclassesclasskeywordorder-) 🔧
 - [SlevomatCodingStandard.Classes.ForbiddenPublicProperty](doc/classes.md#slevomatcodingstandardclassesforbiddenpublicproperty)
 - [SlevomatCodingStandard.Classes.MethodSpacing](doc/classes.md#slevomatcodingstandardclassesmethodspacing-) 🔧
 - [SlevomatCodingStandard.Classes.ModernClassNameReference](doc/classes.md#slevomatcodingstandardclassesmodernclassnamereference-) 🔧
 - [SlevomatCodingStandard.Classes.ParentCallSpacing](doc/classes.md#slevomatcodingstandardclassesparentcallspacing-) 🔧
 - [SlevomatCodingStandard.Classes.PropertyDeclaration](doc/classes.md#slevomatcodingstandardclassespropertydeclaration-) 🔧
 - [SlevomatCodingStandard.Classes.PropertySpacing](doc/classes.md#slevomatcodingstandardclassespropertyspacing-) 🔧
 - [SlevomatCodingStandard.Classes.RequireAbstractOrFinal](doc/classes.md#slevomatcodingstandardclassesrequireabstractorfinal-) 🔧
 - [SlevomatCodingStandard.Classes.RequireConstructorPropertyPromotion](doc/classes.md#slevomatcodingstandardclassesrequireconstructorpropertypromotion-) 🔧
 - [SlevomatCodingStandard.Classes.RequireMultiLineMethodSignature](doc/classes.md#slevomatcodingstandardclassesrequiremultilinemethodsignature-) 🔧
 - [SlevomatCodingStandard.Classes.RequireSelfReference](doc/classes.md#slevomatcodingstandardclassesrequireselfreference-) 🔧
 - [SlevomatCodingStandard.Classes.RequireSingleLineMethodSignature](doc/classes.md#slevomatcodingstandardclassesrequiresinglelinemethodsignature-) 🔧
 - [SlevomatCodingStandard.Classes.SuperfluousAbstractClassNaming](doc/classes.md#slevomatcodingstandardclassessuperfluousabstractclassnaming)
 - [SlevomatCodingStandard.Classes.SuperfluousErrorNaming](doc/classes.md#slevomatcodingstandardclassessuperfluouserrornaming)
 - [SlevomatCodingStandard.Classes.SuperfluousExceptionNaming](doc/classes.md#slevomatcodingstandardclassessuperfluousexceptionnaming)
 - [SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming](doc/classes.md#slevomatcodingstandardclassessuperfluousinterfacenaming)
 - [SlevomatCodingStandard.Classes.SuperfluousTraitNaming](doc/classes.md#slevomatcodingstandardclassessuperfluoustraitnaming)
 - [SlevomatCodingStandard.Classes.TraitUseDeclaration](doc/classes.md#slevomatcodingstandardclassestraitusedeclaration-) 🔧
 - [SlevomatCodingStandard.Classes.TraitUseSpacing](doc/classes.md#slevomatcodingstandardclassestraitusespacing-) 🔧
 - [SlevomatCodingStandard.Classes.UselessLateStaticBinding](doc/classes.md#slevomatcodingstandardclassesuselesslatestaticbinding-) 🔧
 - [SlevomatCodingStandard.Commenting.AnnotationName](doc/commenting.md#slevomatcodingstandardcommentingannotationname-)
 - [SlevomatCodingStandard.Commenting.DeprecatedAnnotationDeclaration](doc/commenting.md#slevomatcodingstandardcommentingdeprecatedannotationdeclaration)
 - [SlevomatCodingStandard.Commenting.DisallowCommentAfterCode](doc/commenting.md#slevomatcodingstandardcommentingdisallowcommentaftercode-) 🔧
 - [SlevomatCodingStandard.Commenting.DisallowOneLinePropertyDocComment](doc/commenting.md#slevomatcodingstandardcommentingdisallowonelinepropertydoccomment-) 🔧
 - [SlevomatCodingStandard.Commenting.DocCommentSpacing](doc/commenting.md#slevomatcodingstandardcommentingdoccommentspacing-) 🔧
 - [SlevomatCodingStandard.Commenting.EmptyComment](doc/commenting.md#slevomatcodingstandardcommentingemptycomment-) 🔧
 - [SlevomatCodingStandard.Commenting.ForbiddenAnnotations](doc/commenting.md#slevomatcodingstandardcommentingforbiddenannotations-) 🔧
 - [SlevomatCodingStandard.Commenting.ForbiddenComments](doc/commenting.md#slevomatcodingstandardcommentingforbiddencomments-) 🔧
 - [SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration](doc/commenting.md#slevomatcodingstandardcommentinginlinedoccommentdeclaration-) 🔧
 - [SlevomatCodingStandard.Commenting.RequireOneDocComment](doc/commenting.md#slevomatcodingstandardcommentingrequireonedoccomment-) 🔧
 - [SlevomatCodingStandard.Commenting.RequireOneLineDocComment](doc/commenting.md#slevomatcodingstandardcommentingrequireonelinedoccomment-) 🔧
 - [SlevomatCodingStandard.Commenting.RequireOneLinePropertyDocComment](doc/commenting.md#slevomatcodingstandardcommentingrequireonelinepropertydoccomment-) 🔧
 - [SlevomatCodingStandard.Commenting.UselessFunctionDocComment](doc/commenting.md#slevomatcodingstandardcommentinguselessfunctiondoccomment-) 🔧
 - [SlevomatCodingStandard.Commenting.UselessInheritDocComment](doc/commenting.md#slevomatcodingstandardcommentinguselessinheritdoccomment-) 🔧
 - [SlevomatCodingStandard.Complexity.Cognitive](doc/complexity.md#slevomatcodingstandardcomplexitycognitive)
 - [SlevomatCodingStandard.ControlStructures.AssignmentInCondition](doc/control-structures.md#slevomatcodingstandardcontrolstructuresassignmentincondition)
 - [SlevomatCodingStandard.ControlStructures.BlockControlStructureSpacing](doc/control-structures.md#slevomatcodingstandardcontrolstructuresblockcontrolstructurespacing-) 🔧
 - [SlevomatCodingStandard.ControlStructures.DisallowContinueWithoutIntegerOperandInSwitch](doc/control-structures.md#slevomatcodingstandardcontrolstructuresdisallowcontinuewithoutintegeroperandinswitch-) 🔧
 - [SlevomatCodingStandard.ControlStructures.DisallowEmpty](doc/control-structures.md#slevomatcodingstandardcontrolstructuresdisallowempty)
 - [SlevomatCodingStandard.ControlStructures.DisallowNullSafeObjectOperator](doc/control-structures.md#slevomatcodingstandardcontrolstructuresdisallownullsafeobjectoperator)
 - [SlevomatCodingStandard.ControlStructures.DisallowShortTernaryOperator](doc/control-structures.md#slevomatcodingstandardcontrolstructuresdisallowshortternaryoperator-) 🔧
 - [SlevomatCodingStandard.ControlStructures.DisallowTrailingMultiLineTernaryOperatorSniff](doc/control-structures.md#slevomatcodingstandardcontrolstructuresdisallowtrailingmultilineternaryoperator-) 🔧
 - [SlevomatCodingStandard.ControlStructures.DisallowYodaComparison](doc/control-structures.md#slevomatcodingstandardcontrolstructuresdisallowyodacomparison-) 🔧
 - [SlevomatCodingStandard.ControlStructures.EarlyExit](doc/control-structures.md#slevomatcodingstandardcontrolstructuresearlyexit-) 🔧
 - [SlevomatCodingStandard.ControlStructures.JumpStatementsSpacing](doc/control-structures.md#slevomatcodingstandardcontrolstructuresjumpstatementsspacing-) 🔧
 - [SlevomatCodingStandard.ControlStructures.LanguageConstructWithParentheses](doc/control-structures.md#slevomatcodingstandardcontrolstructureslanguageconstructwithparentheses-) 🔧
 - [SlevomatCodingStandard.ControlStructures.NewWithParentheses](doc/control-structures.md#slevomatcodingstandardcontrolstructuresnewwithparentheses-) 🔧
 - [SlevomatCodingStandard.ControlStructures.NewWithoutParentheses](doc/control-structures.md#slevomatcodingstandardcontrolstructuresnewwithoutparentheses-) 🔧
 - [SlevomatCodingStandard.ControlStructures.RequireMultiLineCondition](doc/control-structures.md#slevomatcodingstandardcontrolstructuresrequiremultilinecondition-) 🔧
 - [SlevomatCodingStandard.ControlStructures.RequireMultiLineTernaryOperator](doc/control-structures.md#slevomatcodingstandardcontrolstructuresrequiremultilineternaryoperator-) 🔧
 - [SlevomatCodingStandard.ControlStructures.RequireNullCoalesceEqualOperator](doc/control-structures.md#slevomatcodingstandardcontrolstructuresrequirenullcoalesceequaloperator-) 🔧
 - [SlevomatCodingStandard.ControlStructures.RequireNullCoalesceOperator](doc/control-structures.md#slevomatcodingstandardcontrolstructuresrequirenullcoalesceoperator-) 🔧
 - [SlevomatCodingStandard.ControlStructures.RequireNullSafeObjectOperator](doc/control-structures.md#slevomatcodingstandardcontrolstructuresrequirenullsafeobjectoperator-) 🔧
 - [SlevomatCodingStandard.ControlStructures.RequireShortTernaryOperator](doc/control-structures.md#slevomatcodingstandardcontrolstructuresrequireshortternaryoperator-) 🔧
 - [SlevomatCodingStandard.ControlStructures.RequireSingleLineCondition](doc/control-structures.md#slevomatcodingstandardcontrolstructuresrequiresinglelinecondition-) 🔧
 - [SlevomatCodingStandard.ControlStructures.RequireTernaryOperator](doc/control-structures.md#slevomatcodingstandardcontrolstructuresrequireternaryoperator-) 🔧
 - [SlevomatCodingStandard.ControlStructures.RequireYodaComparison](doc/control-structures.md#slevomatcodingstandardcontrolstructuresrequireyodacomparison-) 🔧
 - [SlevomatCodingStandard.ControlStructures.UselessIfConditionWithReturn](doc/control-structures.md#slevomatcodingstandardcontrolstructuresuselessifconditionwithreturn-) 🔧
 - [SlevomatCodingStandard.ControlStructures.UselessTernaryOperator](doc/control-structures.md#slevomatcodingstandardcontrolstructuresuselessternaryoperator-) 🔧
 - [SlevomatCodingStandard.Exceptions.DeadCatch](doc/exceptions.md#slevomatcodingstandardexceptionsdeadcatch)
 - [SlevomatCodingStandard.Exceptions.DisallowNonCapturingCatch](doc/exceptions.md#slevomatcodingstandardexceptionsdisallownoncapturingcatch)
 - [SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly](doc/exceptions.md#slevomatcodingstandardexceptionsreferencethrowableonly-) 🔧🚧
 - [SlevomatCodingStandard.Exceptions.RequireNonCapturingCatch](doc/exceptions.md#slevomatcodingstandardexceptionsrequirenoncapturingcatch-) 🔧
 - [SlevomatCodingStandard.Files.FileLength](doc/files.md#slevomatcodingstandardfilesfilelength)
 - [SlevomatCodingStandard.Files.LineLength](doc/files.md#slevomatcodingstandardfileslinelength)
 - [SlevomatCodingStandard.Files.TypeNameMatchesFileName](doc/files.md#slevomatcodingstandardfilestypenamematchesfilename)
 - [SlevomatCodingStandard.Functions.ArrowFunctionDeclaration](doc/functions.md#slevomatcodingstandardfunctionsarrowfunctiondeclaration-) 🔧
 - [SlevomatCodingStandard.Functions.DisallowArrowFunction](doc/functions.md#slevomatcodingstandardfunctionsdisallowarrowfunction)
 - [SlevomatCodingStandard.Functions.DisallowEmptyFunction](doc/functions.md#slevomatcodingstandardfunctionsdisallowemptyfunction)
 - [SlevomatCodingStandard.Functions.DisallowNamedArguments](doc/functions.md#slevomatcodingstandardfunctionsdisallownamedarguments)
 - [SlevomatCodingStandard.Functions.DisallowTrailingCommaInCall](doc/functions.md#slevomatcodingstandardfunctionsdisallowtrailingcommaincall-) 🔧
 - [SlevomatCodingStandard.Functions.DisallowTrailingCommaInClosureUse](doc/functions.md#slevomatcodingstandardfunctionsdisallowtrailingcommainclosureuse-) 🔧
 - [SlevomatCodingStandard.Functions.DisallowTrailingCommaInDeclaration](doc/functions.md#slevomatcodingstandardfunctionsdisallowtrailingcommaindeclaration-) 🔧
 - [SlevomatCodingStandard.Functions.FunctionLength](doc/functions.md#slevomatcodingstandardfunctionsfunctionlength)
 - [SlevomatCodingStandard.Functions.NamedArgumentSpacing](doc/functions.md#slevomatcodingstandardfunctionsnamedargumentspacing-) 🔧
 - [SlevomatCodingStandard.Functions.RequireArrowFunction](doc/functions.md#slevomatcodingstandardfunctionsrequirearrowfunction-) 🔧
 - [SlevomatCodingStandard.Functions.RequireMultiLineCall](doc/functions.md#slevomatcodingstandardfunctionsrequiremultilinecall-) 🔧
 - [SlevomatCodingStandard.Functions.RequireSingleLineCall](doc/functions.md#slevomatcodingstandardfunctionsrequiresinglelinecall-) 🔧
 - [SlevomatCodingStandard.Functions.RequireTrailingCommaInCall](doc/functions.md#slevomatcodingstandardfunctionsrequiretrailingcommaincall-) 🔧
 - [SlevomatCodingStandard.Functions.RequireTrailingCommaInClosureUse](doc/functions.md#slevomatcodingstandardfunctionsrequiretrailingcommainclosureuse-) 🔧
 - [SlevomatCodingStandard.Functions.RequireTrailingCommaInDeclaration](doc/functions.md#slevomatcodingstandardfunctionsrequiretrailingcommaindeclaration-) 🔧
 - [SlevomatCodingStandard.Functions.StaticClosure](doc/functions.md#slevomatcodingstandardfunctionsstaticclosure-) 🔧
 - [SlevomatCodingStandard.Functions.StrictCall](doc/functions.md#slevomatcodingstandardfunctionsstrictcall)
 - [SlevomatCodingStandard.Functions.UnusedInheritedVariablePassedToClosure](doc/functions.md#slevomatcodingstandardfunctionsunusedinheritedvariablepassedtoclosure-) 🔧
 - [SlevomatCodingStandard.Functions.UnusedParameter](doc/functions.md#slevomatcodingstandardfunctionsunusedparameter-) 🚧
 - [SlevomatCodingStandard.Functions.UselessParameterDefaultValue](doc/functions.md#slevomatcodingstandardfunctionsuselessparameterdefaultvalue-) 🚧
 - [SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses](doc/namespaces.md#slevomatcodingstandardnamespacesalphabeticallysorteduses-) 🔧
 - [SlevomatCodingStandard.Namespaces.DisallowGroupUse](doc/namespaces.md#slevomatcodingstandardnamespacesdisallowgroupuse)
 - [SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation](doc/namespaces.md#slevomatcodingstandardnamespacesfullyqualifiedclassnameinannotation-) 🔧
 - [SlevomatCodingStandard.Namespaces.FullyQualifiedExceptions](doc/namespaces.md#slevomatcodingstandardnamespacesfullyqualifiedexceptions-) 🔧
 - [SlevomatCodingStandard.Namespaces.FullyQualifiedGlobalConstants](doc/namespaces.md#slevomatcodingstandardnamespacesfullyqualifiedglobalconstants-) 🔧
 - [SlevomatCodingStandard.Namespaces.FullyQualifiedGlobalFunctions](doc/namespaces.md#slevomatcodingstandardnamespacesfullyqualifiedglobalfunctions-) 🔧
 - [SlevomatCodingStandard.Namespaces.MultipleUsesPerLine](doc/namespaces.md#slevomatcodingstandardnamespacesmultipleusesperline)
 - [SlevomatCodingStandard.Namespaces.NamespaceDeclaration](doc/namespaces.md#slevomatcodingstandardnamespacesnamespacedeclaration-) 🔧
 - [SlevomatCodingStandard.Namespaces.NamespaceSpacing](doc/namespaces.md#slevomatcodingstandardnamespacesnamespacespacing-) 🔧
 - [SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly](doc/namespaces.md#slevomatcodingstandardnamespacesreferenceusednamesonly-) 🔧
 - [SlevomatCodingStandard.Namespaces.RequireOneNamespaceInFile](doc/namespaces.md#slevomatcodingstandardnamespacesrequireonenamespaceinfile)
 - [SlevomatCodingStandard.Namespaces.UnusedUses](doc/namespaces.md#slevomatcodingstandardnamespacesunuseduses-) 🔧
 - [SlevomatCodingStandard.Namespaces.UseDoesNotStartWithBackslash](doc/namespaces.md#slevomatcodingstandardnamespacesusedoesnotstartwithbackslash-) 🔧
 - [SlevomatCodingStandard.Namespaces.UseFromSameNamespace](doc/namespaces.md#slevomatcodingstandardnamespacesusefromsamenamespace-) 🔧
 - [SlevomatCodingStandard.Namespaces.UseOnlyWhitelistedNamespaces](doc/namespaces.md#slevomatcodingstandardnamespacesuseonlywhitelistednamespaces)
 - [SlevomatCodingStandard.Namespaces.UseSpacing](doc/namespaces.md#slevomatcodingstandardnamespacesusespacing-) 🔧
 - [SlevomatCodingStandard.Namespaces.UselessAlias](doc/namespaces.md#slevomatcodingstandardnamespacesuselessalias-) 🔧
 - [SlevomatCodingStandard.Numbers.DisallowNumericLiteralSeparator](doc/numbers.md#slevomatcodingstandardnumbersdisallownumericliteralseparator-) 🔧
 - [SlevomatCodingStandard.Numbers.RequireNumericLiteralSeparator](doc/numbers.md#slevomatcodingstandardnumbersrequirenumericliteralseparator)
 - [SlevomatCodingStandard.Operators.DisallowEqualOperators](doc/operators.md#slevomatcodingstandardoperatorsdisallowequaloperators-) 🔧
 - [SlevomatCodingStandard.Operators.DisallowIncrementAndDecrementOperators](doc/operators.md#slevomatcodingstandardoperatorsdisallowincrementanddecrementoperators)
 - [SlevomatCodingStandard.Operators.NegationOperatorSpacing](doc/operators.md#slevomatcodingstandardoperatorsnegationoperatorspacing-) 🔧
 - [SlevomatCodingStandard.Operators.RequireCombinedAssignmentOperator](doc/operators.md#slevomatcodingstandardoperatorsrequirecombinedassignmentoperator-) 🔧
 - [SlevomatCodingStandard.Operators.RequireOnlyStandaloneIncrementAndDecrementOperators](doc/operators.md#slevomatcodingstandardoperatorsrequireonlystandaloneincrementanddecrementoperators)
 - [SlevomatCodingStandard.Operators.SpreadOperatorSpacing](doc/operators.md#slevomatcodingstandardoperatorsspreadoperatorspacing-) 🔧
 - [SlevomatCodingStandard.PHP.DisallowDirectMagicInvokeCall](doc/php.md#slevomatcodingstandardphpdisallowdirectmagicinvokecall-) 🔧
 - [SlevomatCodingStandard.PHP.DisallowReference](doc/php.md#slevomatcodingstandardphpdisallowreference)
 - [SlevomatCodingStandard.PHP.ForbiddenClasses](doc/php.md#slevomatcodingstandardphpforbiddenclasses-) 🔧
 - [SlevomatCodingStandard.PHP.OptimizedFunctionsWithoutUnpacking](doc/php.md#slevomatcodingstandardphpoptimizedfunctionswithoutunpacking)
 - [SlevomatCodingStandard.PHP.ReferenceSpacing](doc/php.md#slevomatcodingstandardphpreferencespacing-) 🔧
 - [SlevomatCodingStandard.PHP.RequireExplicitAssertion](doc/php.md#slevomatcodingstandardphprequireexplicitassertion-) 🔧
 - [SlevomatCodingStandard.PHP.RequireNowdoc](doc/php.md#slevomatcodingstandardphprequirenowdoc-) 🔧
 - [SlevomatCodingStandard.PHP.ShortList](doc/php.md#slevomatcodingstandardphpshortlist-) 🔧
 - [SlevomatCodingStandard.PHP.TypeCast](doc/php.md#slevomatcodingstandardphptypecast-) 🔧
 - [SlevomatCodingStandard.PHP.UselessParentheses](doc/php.md#slevomatcodingstandardphpuselessparentheses-) 🔧
 - [SlevomatCodingStandard.PHP.UselessSemicolon](doc/php.md#slevomatcodingstandardphpuselesssemicolon-) 🔧
 - [SlevomatCodingStandard.Strings.DisallowVariableParsing](doc/strings.md#slevomatcodingstandardstringsdisallowvariableparsing)
 - [SlevomatCodingStandard.TypeHints.ClassConstantTypeHint](doc/type-hints.md#slevomatcodingstandardtypehintsclassconstanttypehint-) 🔧
 - [SlevomatCodingStandard.TypeHints.DeclareStrictTypes](doc/type-hints.md#slevomatcodingstandardtypehintsdeclarestricttypes-) 🔧
 - [SlevomatCodingStandard.TypeHints.DisallowArrayTypeHintSyntax](doc/type-hints.md#slevomatcodingstandardtypehintsdisallowarraytypehintsyntax-) 🔧
 - [SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint](doc/type-hints.md#slevomatcodingstandardtypehintsdisallowmixedtypehint)
 - [SlevomatCodingStandard.TypeHints.DNFTypeHintFormat](doc/type-hints.md#slevomatcodingstandardtypehintsdnftypehintformat-) 🔧
 - [SlevomatCodingStandard.TypeHints.LongTypeHints](doc/type-hints.md#slevomatcodingstandardtypehintslongtypehints-) 🔧
 - [SlevomatCodingStandard.TypeHints.NullTypeHintOnLastPosition](doc/type-hints.md#slevomatcodingstandardtypehintsnulltypehintonlastposition-) 🔧
 - [SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue](doc/type-hints.md#slevomatcodingstandardtypehintsnullabletypefornulldefaultvalue-) 🔧🚧
 - [SlevomatCodingStandard.TypeHints.ParameterTypeHint](doc/type-hints.md#slevomatcodingstandardtypehintsparametertypehint-) 🔧🚧
 - [SlevomatCodingStandard.TypeHints.ParameterTypeHintSpacing](doc/type-hints.md#slevomatcodingstandardtypehintsparametertypehintspacing-) 🔧
 - [SlevomatCodingStandard.TypeHints.PropertyTypeHint](doc/type-hints.md#slevomatcodingstandardtypehintspropertytypehint-) 🔧🚧
 - [SlevomatCodingStandard.TypeHints.ReturnTypeHint](doc/type-hints.md#slevomatcodingstandardtypehintsreturntypehint-) 🔧🚧
 - [SlevomatCodingStandard.TypeHints.ReturnTypeHintSpacing](doc/type-hints.md#slevomatcodingstandardtypehintsreturntypehintspacing-) 🔧
 - [SlevomatCodingStandard.TypeHints.UnionTypeHintFormat](doc/type-hints.md#slevomatcodingstandardtypehintsuniontypehintformat-) 🔧
 - [SlevomatCodingStandard.TypeHints.UselessConstantTypeHint](doc/type-hints.md#slevomatcodingstandardtypehintsuselessconstanttypehint-) 🔧
 - [SlevomatCodingStandard.Variables.DisallowVariableVariable](doc/variables.md#slevomatcodingstandardvariablesdisallowvariablevariable)
 - [SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable](doc/variables.md#slevomatcodingstandardvariablesdisallowsuperglobalvariable)
 - [SlevomatCodingStandard.Variables.DuplicateAssignmentToVariable](doc/variables.md#slevomatcodingstandardvariablesduplicateassignmenttovariable)
 - [SlevomatCodingStandard.Variables.UnusedVariable](doc/variables.md#slevomatcodingstandardvariablesunusedvariable)
 - [SlevomatCodingStandard.Variables.UselessVariable](doc/variables.md#slevomatcodingstandardvariablesuselessvariable-) 🔧
 - [SlevomatCodingStandard.Whitespaces.DuplicateSpaces](doc/whitespaces.md#slevomatcodingstandardwhitespacesduplicatespaces-) 🔧

## Installation

The recommended way to install Slevomat Coding Standard is [through Composer](http://getcomposer.org).

```JSON
{
	"require-dev": {
		"slevomat/coding-standard": "~8.0"
	}
}
```

It's also recommended to install [php-parallel-lint/php-parallel-lint](https://github.com/php-parallel-lint/PHP-Parallel-Lint) which checks source code for syntax errors. Sniffs count on the processed code to be syntactically valid (no parse errors), otherwise they can behave unexpectedly. It is advised to run `PHP-Parallel-Lint` in your build tool before running `PHP_CodeSniffer` and exiting the build process early if `PHP-Parallel-Lint` fails.

## How to run the sniffs

You can choose one of two ways to run only selected sniffs from the standard on your codebase:

### Choose which sniffs to run

The recommended way is to write your own ruleset.xml by referencing only the selected sniffs. This is a sample ruleset.xml:

```xml
<?xml version="1.0"?>
<ruleset name="AcmeProject">
	<config name="installed_paths" value="../../slevomat/coding-standard"/><!-- relative path from PHPCS source location -->
	<rule ref="SlevomatCodingStandard.Arrays.TrailingArrayComma"/>
	<!-- other sniffs to include -->
</ruleset>
```

Then run the `phpcs` executable the usual way:

```
vendor/bin/phpcs --standard=ruleset.xml --extensions=php --tab-width=4 -sp src tests
```

### Exclude sniffs you don't want to run

You can also mention Slevomat Coding Standard in your project's `ruleset.xml` and exclude only some sniffs:

```xml
<?xml version="1.0"?>
<ruleset name="AcmeProject">
	<rule ref="vendor/slevomat/coding-standard/SlevomatCodingStandard/ruleset.xml"><!-- relative path to your ruleset.xml -->
		<!-- sniffs to exclude -->
	</rule>
</ruleset>
```

However it is not a recommended way to use Slevomat Coding Standard, because your build can break when moving between minor versions of the standard (which can happen if you use `^` or `~` version constraint in `composer.json`). We regularly add new sniffs even in minor versions meaning your code won't most likely comply with new minor versions of the package.

## Fixing errors automatically

Sniffs in this standard marked by the 🔧 symbol support [automatic fixing of coding standard violations](https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically). To fix your code automatically, run phpcbf instead of phpcs:

```
vendor/bin/phpcbf --standard=ruleset.xml --extensions=php --tab-width=4 -sp src tests
```

Always remember to back up your code before performing automatic fixes and check the results with your own eyes as the automatic fixer can sometimes produce unwanted results.

## Suppressing sniffs locally

Selected sniffs in this standard marked by the 🚧 symbol can be suppressed for a specific piece of code using an annotation. Consider the following example:

```php
/**
 * @param int $max
 */
public function createProgressBar($max = 0): ProgressBar
{

}
```

The parameter `$max` could have a native `int` scalar typehint. But because the method in the parent class does not have this typehint, so this one cannot have it either. PHP_CodeSniffer shows a following error:

```
----------------------------------------------------------------------
FOUND 1 ERROR AFFECTING 1 LINE
----------------------------------------------------------------------
 67 | ERROR | [x] Method ErrorsConsoleStyle::createProgressBar()
    |       |     does not have native type hint for its parameter $max
    |       |     but it should be possible to add it based on @param
    |       |     annotation "int".
    |       |     (SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint)
```

If we want to suppress this error instead of fixing it, we can take the error code (`SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint`) and use it with a `@phpcsSuppress` annotation like this:

```php
/**
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
 * @param int $max
 */
public function createProgressBar($max = 0): ProgressBar
{

}
```

## Contributing

To make this repository work on your machine, clone it and run these two commands in the root directory of the repository:

```
composer install
bin/phing
```

After writing some code and editing or adding unit tests, run phing again to check that everything is OK:

```
bin/phing
```

We are always looking forward to your bugreports, feature requests and pull requests. Thank you.

## Code of Conduct

This project adheres to a [Contributor Code of Conduct](https://github.com/slevomat/coding-standard/blob/master/CODE_OF_CONDUCT.md). By participating in this project and its community, you are expected to uphold this code.
