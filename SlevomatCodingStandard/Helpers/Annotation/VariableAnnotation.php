<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use function sprintf;

/**
 * @internal
 */
class VariableAnnotation extends Annotation
{

	/** @var VarTagValueNode|null */
	private $contentNode;

	public function __construct(string $name, int $startPointer, int $endPointer, ?string $content, ?VarTagValueNode $contentNode)
	{
		if ($name !== '@var') {
			throw new InvalidArgumentException(sprintf('Unsupported annotation %s.', $name));
		}

		parent::__construct($name, $startPointer, $endPointer, $content);

		$this->contentNode = $contentNode;
	}

	public function isInvalid(): bool
	{
		return $this->contentNode === null;
	}

	public function getContentNode(): VarTagValueNode
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
			throw new LogicException('Invalid @var annotation.');
		}

		return $this->contentNode->description !== '' ? $this->contentNode->description : null;
	}

	public function getVariableName(): ?string
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		return $this->contentNode->variableName !== '' ? $this->contentNode->variableName : null;
	}

	/**
	 * @return GenericTypeNode|CallableTypeNode|IntersectionTypeNode|UnionTypeNode|ArrayTypeNode|IdentifierTypeNode|ThisTypeNode|ArrayShapeNode|NullableTypeNode
	 */
	public function getType(): TypeNode
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		/** @var GenericTypeNode|CallableTypeNode|IntersectionTypeNode|UnionTypeNode|ArrayTypeNode|IdentifierTypeNode|ThisTypeNode|ArrayShapeNode|NullableTypeNode $type */
		$type = $this->contentNode->type;
		return $type;
	}

	public function export(): string
	{
		$exported = sprintf('%s %s', $this->name, AnnotationTypeHelper::export($this->getType()));

		$variableName = $this->getVariableName();
		if ($variableName !== null) {
			$exported .= sprintf(' %s', $variableName);
		}

		$description = $this->getDescription();
		if ($description !== null) {
			$exported .= sprintf(' %s', $this->fixDescription($description));
		}

		return $exported;
	}

}
