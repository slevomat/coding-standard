<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use PHPStan\PhpDocParser\Ast\PhpDoc\SelfOutTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\CallableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ConstTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use function in_array;
use function sprintf;

/**
 * @internal
 */
class SelfOutAnnotation extends Annotation
{

	/** @var SelfOutTagValueNode|null */
	private $contentNode;

	public function __construct(string $name, int $startPointer, int $endPointer, ?string $content, ?SelfOutTagValueNode $contentNode)
	{
		if (!in_array($name, ['@psalm-this-out', '@phpstan-this-out', '@psalm-self-out', '@phpstan-self-out'], true)) {
			throw new InvalidArgumentException(sprintf('Unsupported annotation %s.', $name));
		}

		parent::__construct($name, $startPointer, $endPointer, $content);

		$this->contentNode = $contentNode;
	}

	public function isInvalid(): bool
	{
		return $this->contentNode === null;
	}

	public function getContentNode(): ?SelfOutTagValueNode
	{
		$this->errorWhenInvalid();

		return $this->contentNode;
	}

	public function hasDescription(): bool
	{
		return $this->getDescription() !== null;
	}

	public function getDescription(): ?string
	{
		$this->errorWhenInvalid();

		return $this->contentNode->description !== '' ? $this->contentNode->description : null;
	}

	/**
	 * @return GenericTypeNode|CallableTypeNode|IntersectionTypeNode|UnionTypeNode|ArrayTypeNode|ArrayShapeNode|IdentifierTypeNode|ThisTypeNode|NullableTypeNode|ConstTypeNode
	 */
	public function getType(): TypeNode
	{
		$this->errorWhenInvalid();

		/** @var GenericTypeNode|CallableTypeNode|IntersectionTypeNode|UnionTypeNode|ArrayTypeNode|ArrayShapeNode|IdentifierTypeNode|ThisTypeNode|NullableTypeNode|ConstTypeNode $type */
		$type = $this->contentNode->type;
		return $type;
	}

	public function export(): string
	{
		$exported = sprintf(
			'%s %s',
			$this->name,
			AnnotationTypeHelper::export($this->getType())
		);

		$description = $this->getDescription();
		if ($description !== null) {
			$exported .= sprintf(' %s', $this->fixDescription($description));
		}

		return $exported;
	}

}
