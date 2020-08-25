<?php

namespace FooNamespace{

	use DB;
	use UserModel;
	use SomeClass;

	class FooClass extends \FQNExtendedClass implements \ImplementedInterface, SecondImplementedInterface, \ThirdImplementedInterface
	{

		use \FullyQualified\SomeOtherTrait,
			SomeDifferentTrait, \FullyQualified\SometTotallyDifferentTrait /** also after */;
		use SomeTraitA, \SomeTraitB {
			SomeTraitA::someMethods insteadof \SomeTraitB;
		}

		public function fooMethod()
		{
			$classToCreateFromString = "\SomeClassMadeFromString";
			$interpretedClass = new $classToCreateFromString;

			$classicClass = new SomeInstanceClass($withParams);
			$classWithoutParams = new Nested\SomeClass;
			$classWithoutParamsAndMethodCall = (new \FullyQualified\Nested\SomeClass)->someMethod();

			$testNewSelf = new self("foo");

			$anonClass = new class () extends ForbiddenExtendedClass implements \ImplementedInterface, \SecondImplementedInterface
			{
				use \FullyQualified\SometTotallyDifferentTrait;
			};
		}

		public function doubleCollonCases($testClass)
		{
			$constant = $testClass::ACCESS_CONSTANT;
			$staticVar = DB::$variable;

			$user = UserModel::getQuery();

			$user->where(function (DB $query) {
				return UserModel::addNotBlockedToQuery($query);
			});

			$queryParts = [
				DB::select('id'),
				DB::from(DB::raw('users')),
				DB::where(UserModel::class, 'active', true),
			];

			DB::query('INSERT INTO ...');

			$this->getModel()::query();

			parent::doubleCollonCases();

			return DB::exec($queryParts);
		}
	}
}

namespace BarNamespace {
	trait FooTrait
	{
		use \SomeTraitB;
	}
}
