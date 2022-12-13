<?php // lint >= 8.0

/** Class comment */
#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]

#[Attribute4(), Attribute5]

class Whatever
{

	#[Attribute1, Attribute2('var'), Attribute3(option: PDO::class, option2: true, option3: 'False')]
	#[Attribute4(), Attribute5]

	/**
	 * Method comment
	 */
	public function method(
		#[Attribute1]


		$parameter,
		#[Attribute2] $parameter2
	)
	{
	}

	public function __construct(
		#[ORM\Column(unique: true)] protected string $name,
		#[ORM\Cache] #[ORM\ManyToOne(targetEntity: 'State', inversedBy: 'cities')] #[ORM\JoinColumn(name: 'state_id', referencedColumnName: 'id')] protected State|null $state = null,
	) {
	}

}
