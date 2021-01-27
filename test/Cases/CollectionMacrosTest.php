<?php


	namespace MehrItLaraModelAspectsTests\Cases;


	use Illuminate\Database\Eloquent\Collection;
	use MehrItLaraModelAspectsTests\Model\Aspects\TestModelAspects;
	use MehrItLaraModelAspectsTests\Model\TestModel;

	class CollectionMacrosTest extends TestCase
	{

		public function testCollectionSetAspects() {

			$asp = new TestModelAspects();

			$collection = collect([new TestModel(), new TestModel()])->setAspects($asp);

			$collection[0]->withAspects(function($aspects) use ($asp) {
				$this->assertSame($asp, $aspects);
			});
			$collection[1]->withAspects(function($aspects) use ($asp) {
				$this->assertSame($asp, $aspects);
			});

		}

		public function testEloquentCollectionSetAspects() {

			$asp = new TestModelAspects();

			$collection = (new Collection([new TestModel(), new TestModel()]))->setAspects($asp);

			$collection[0]->withAspects(function($aspects) use ($asp) {
				$this->assertSame($asp, $aspects);
			});
			$collection[1]->withAspects(function($aspects) use ($asp) {
				$this->assertSame($asp, $aspects);
			});

		}

	}