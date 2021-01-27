<?php


	namespace MehrItLaraModelAspectsTests\Cases;


	use MehrItLaraModelAspectsTests\Model\Aspects\TestModelAspects;
	use MehrItLaraModelAspectsTests\Model\TestModel;
	use RuntimeException;
	use stdClass;

	class AbstractModelAspectsTest extends TestCase
	{


		public function testWithModel() {

			$model       = new TestModel();
			$modelBefore = new TestModel();


			$asp = new TestModelAspects();
			$asp->setAspectModel($modelBefore);

			$ret = new stdClass();
			$this->assertSame($ret, $asp->withModel($model, function (TestModelAspects $aspects) use ($model, $ret, $asp) {
				$this->assertSame($asp, $aspects);
				$this->assertSame($model, $aspects->getAspectsModel());

				return $ret;
			}));

			$this->assertSame($modelBefore, $asp->getAspectsModel());

		}

		public function testAssertModelSet() {

			$this->expectNotToPerformAssertions();

			$model       = new TestModel();

			$asp = new TestModelAspects();
			$asp->setAspectModel($model);

			$asp->invokeAssertModelSet();
		}

		public function testAssertModelSet_notSet() {

			$this->expectNotToPerformAssertions();

			$asp = new TestModelAspects();

			$this->expectException(RuntimeException::class);
			$asp->invokeAssertModelSet();
		}
	}