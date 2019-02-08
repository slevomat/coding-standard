<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use function implode;
use function sprintf;

/**
 * @internal
 */
class MethodAnnotation extends Annotation
{

	/** @var \PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode|null */
	private $contentNode;

	public function __construct(
		string $name,
		int $startPointer,
		int $endPointer,
		?string $content,
		?MethodTagValueNode $contentNode
	)
	{
		if ($name !== '@method') {
			throw new InvalidArgumentException(sprintf('Unsupported annotation %s.', $name));
		}

		parent::__construct($name, $startPointer, $endPointer, $content);

		$this->contentNode = $contentNode;
	}

	public function isInvalid(): bool
	{
		return $this->contentNode === null;
	}

	public function getContentNode(): MethodTagValueNode
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		return $this->contentNode;
	}

	public function hasDescription(): bool
	{
		return $this->getDescription() !== null;
	}

	public function getDescription(): ?string
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		return $this->contentNode->description !== '' ? $this->contentNode->description : null;
	}

	public function getMethodName(): ?string
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		return $this->contentNode->methodName !== '' ? $this->contentNode->methodName : null;
	}

	/**
	 * @return \PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\CallableTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode|\PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode
	 */
	public function getMethodReturnType(): ?TypeNode
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		/** @var \PHPStan\PhpDocParser\Ast\Type\GenericTypeNode|\PHPStan\PhpDocParser\Ast\Type\CallableTypeNode|\PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode|\PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $type */
		$type = $this->contentNode->returnType;
		return $type;
	}

	/**
	 * @return \PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode[]
	 */
	public function getMethodParameters(): array
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		return $this->contentNode->parameters;
	}

	public function export(): string
	{
		$static = $this->contentNode->isStatic ? 'static ' : '';
		$returnType = $this->getMethodReturnType() !== null ? sprintf('%s ', AnnotationTypeHelper::export($this->getMethodReturnType())) : '';

		$parameters = [];
		foreach ($this->getMethodParameters() as $parameter) {
			$type = $parameter->type !== null ? AnnotationTypeHelper::export($parameter->type) . ' ' : '';
			$isReference = $parameter->isReference ? '&' : '';
			$isVariadic = $parameter->isVariadic ? '...' : '';
			$default = $parameter->defaultValue !== null ? sprintf(' = %s', $parameter->defaultValue) : '';

			$parameters[] = sprintf('%s%s%s%s%s', $type, $isReference, $isVariadic, $parameter->parameterName, $default);
		}

		$exported = sprintf('%s %s%s%s(%s)', $this->name, $static, $returnType, $this->getMethodName(), implode(', ', $parameters));

		$description = $this->getDescription();
		if ($description !== null) {
			$exported .= sprintf(' %s', $this->fixDescription($description));
		}

		return $exported;
	}

}
