<?php

namespace FooNamespace{

	use DB;
	use UserModel;
	use SomeClass;

	class FooClass extends \FQNExtendedClass implements \ImplementedInterface, \BarNamespace\SecondImplementedInterface, \ThirdImplementedInterface
	{

		use \FullyQualified\SomeOtherTrait,
			SomeDifferentTrait, \DatabaseTrait /** also after */;
		use SomeTraitA, \SomeTraitB {
			SomeTraitA::someMethods insteadof \SomeTraitB;
		}

		public function fooMethod()
		{
			$classToCreateFromString = "\SomeClassMadeFromString";
			$interpretedClass = new $classToCreateFromString;

			$classicClass = new SomeInstanceClass($withParams);
			$classWithoutParams = new Nested\SomeClass;
			$classWithoutParamsAndMethodCall = (new \FullyQualified\SomeClass)->someMethod();

			$testNewSelf = new self("foo");

			$anonClass = new class () extends \BarNamespace\BaseClass implements \ImplementedInterface, \SecondImplementedInterface
			{
				use \DatabaseTrait;
			};
		}

		public function doubleCollonCases($testClass)
		{
			$constant = $testClass::ACCESS_CONSTANT;
			$staticVar = \TypeORM\DB::$variable;

			$user = \MyApp\Models\User::getQuery();

			$user->where(function (DB $query) {
				return \MyApp\Models\User::addNotBlockedToQuery($query);
			});

			$queryParts = [
				\TypeORM\DB::select('id'),
				\TypeORM\DB::from(\TypeORM\DB::raw('users')),
				\TypeORM\DB::where(\MyApp\Models\User::class, 'active', true),
			];

			\TypeORM\DB::query('INSERT INTO ...');

			$this->getModel()::query();

			parent::doubleCollonCases();

			return \TypeORM\DB::exec($queryParts);
		}
	}
}

namespace BarNamespace {
	trait FooTrait
	{
		use \TotallyDifferentTrait;
	}
}
