<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\PHPStan;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\UnionType;
use function array_merge;
use function array_values;
use function in_array;
use function is_string;
use const T_ATTRIBUTE;
use const T_ATTRIBUTE_END;
use const T_CLOSE_CURLY_BRACKET;
use const T_CLOSE_SHORT_ARRAY;
use const T_CLOSE_SQUARE_BRACKET;
use const T_FN;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_SHORT_ARRAY;
use const T_OPEN_SQUARE_BRACKET;

class GetTokenDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{

	/** @var ArrayType|null */
	private $arrayType;

	public function getClass(): string
	{
		return File::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return $methodReflection->getName() === 'getTokens';
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
	 */
	public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
	{
		return $this->getTokensArrayType();
	}

	private function getTokensArrayType(): ArrayType
	{
		if ($this->arrayType === null) {
			$stringType = new StringType();
			$integerType = new IntegerType();
			$stringIntegerUnion = new UnionType([$stringType, $integerType]);

			$types = [];

			$baseTokenBuilder = ConstantArrayTypeBuilder::createEmpty();
			$baseTokenBuilder->setOffsetValueType(new ConstantStringType('content'), $stringType);
			//$baseTokenBuilder->setOffsetValueType(new ConstantStringType('code'), $stringIntegerUnion);
			$baseTokenBuilder->setOffsetValueType(new ConstantStringType('type'), $stringType);
			$baseTokenBuilder->setOffsetValueType(new ConstantStringType('line'), $integerType);
			$baseTokenBuilder->setOffsetValueType(new ConstantStringType('column'), $integerType);
			$baseTokenBuilder->setOffsetValueType(new ConstantStringType('length'), $integerType);
			$baseTokenBuilder->setOffsetValueType(new ConstantStringType('level'), $integerType);

			$types[] = $baseTokenBuilder->getArray();

			$tokenCodesWithScope = array_merge(
				array_values(Tokens::$scopeOpeners),
				[T_FN]
			);

			$tokenCodesWithBrackets = [
				T_OPEN_SHORT_ARRAY,
				T_CLOSE_SHORT_ARRAY,
				T_OPEN_SQUARE_BRACKET,
				T_CLOSE_SQUARE_BRACKET,
				T_OPEN_CURLY_BRACKET,
				T_CLOSE_CURLY_BRACKET,
			];

			$tokenCodesWithAttributes = [
				T_ATTRIBUTE,
				T_ATTRIBUTE_END,
			];

			foreach (array_merge(
				$tokenCodesWithScope,
				$tokenCodesWithBrackets,
				array_values(Tokens::$parenthesisOpeners),
				array_values(Tokens::$commentTokens),
				$tokenCodesWithAttributes
			) as $tokenCode) {
				$tokenBuilder = clone $baseTokenBuilder;

				$codeType = is_string($tokenCode) ? new ConstantStringType($tokenCode) : new ConstantIntegerType($tokenCode);

				$tokenBuilder->setOffsetValueType(new ConstantStringType('code'), $codeType);

				if (in_array($tokenCode, $tokenCodesWithScope, true)) {
					$tokenBuilder->setOffsetValueType(new ConstantStringType('scope_condition'), $integerType);
					$tokenBuilder->setOffsetValueType(new ConstantStringType('scope_opener'), $integerType);
					$tokenBuilder->setOffsetValueType(new ConstantStringType('scope_closer'), $integerType);
				}

				if (in_array($tokenCode, $tokenCodesWithBrackets, true)) {
					$tokenBuilder->setOffsetValueType(new ConstantStringType('bracket_opener'), $integerType);
					$tokenBuilder->setOffsetValueType(new ConstantStringType('bracket_closer'), $integerType);
				}

				if (in_array($tokenCode, Tokens::$parenthesisOpeners, true)) {
					$tokenBuilder->setOffsetValueType(new ConstantStringType('parenthesis_opener'), $integerType);
					$tokenBuilder->setOffsetValueType(new ConstantStringType('parenthesis_closer'), $integerType);
					$tokenBuilder->setOffsetValueType(new ConstantStringType('parenthesis_owner'), $integerType);
				}

				if (in_array($tokenCode, Tokens::$commentTokens, true)) {
					$tokenBuilder->setOffsetValueType(new ConstantStringType('comment_opener'), $integerType);
					$tokenBuilder->setOffsetValueType(new ConstantStringType('comment_closer'), $integerType);
				}

				if (in_array($tokenCode, $tokenCodesWithAttributes, true)) {
					$tokenBuilder->setOffsetValueType(new ConstantStringType('attribute_opener'), $integerType);
					$tokenBuilder->setOffsetValueType(new ConstantStringType('attribute_closer'), $integerType);
				}

				// Todo
				$tokenBuilder->setOffsetValueType(new ConstantStringType('conditions'), new ArrayType($integerType, $stringIntegerUnion));
				$tokenBuilder->setOffsetValueType(new ConstantStringType('nested_parenthesis'), new ArrayType($integerType, $integerType));

				$types[] = $tokenBuilder->getArray();
			}

			$this->arrayType = new ArrayType($integerType, TypeCombinator::union(...$types));
		}

		return $this->arrayType;
	}

}
