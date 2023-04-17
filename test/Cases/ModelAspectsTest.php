<?php


	namespace MehrItLaraModelAspectsTests\Cases;


	use BadMethodCallException;
	use InvalidArgumentException;
	use MehrItLaraModelAspectsTests\Model\Aspects\TestModelAspects;
	use MehrItLaraModelAspectsTests\Model\TestModel;
	use MehrItLaraModelAspectsTests\Model\TestModelWithParent;
	use MehrItLaraModelAspectsTests\Model\TestModelWithParentAndCallHandler;
	use stdClass;

	class ModelAspectsTest extends TestCase
	{
		public function testNewAspects() {

			$a1 = TestModel::newAspects();
			$a2 = TestModel::newAspects();

			$this->assertInstanceOf(TestModelAspects::class, $a1);
			$this->assertInstanceOf(TestModelAspects::class, $a2);
			$this->assertNotSame($a1, $a2);

		}

		public function testWithAspects() {

			$model = new TestModel();

			$ret = new stdClass();

			$this->assertSame($ret, $model->withAspects(function (TestModelAspects $aspects) use ($model, $ret) {
				$this->assertSame($model, $aspects->getAspectsModel());

				return $ret;
			}));

		}

		public function testSetAspects() {

			$model = new TestModel();

			$asp = TestModel::newAspects();

			$this->assertSame($model, $model->setAspects($asp));


			$model->withAspects(function (TestModelAspects $aspects) use ($model, $asp) {
				$this->assertSame($asp, $aspects);
				$this->assertSame($model, $aspects->getAspectsModel());
			});

		}

		public function testWithAspects_resetsModelAfterwards() {

			$model       = new TestModel();
			$modelBefore = new TestModel();

			$asp = TestModel::newAspects();
			$asp->setAspectModel($modelBefore);

			$model->setAspects($asp);

			$ret = new stdClass();
			$this->assertSame($ret, $model->withAspects(function (TestModelAspects $aspects) use ($model, $ret, $asp) {
				$this->assertSame($asp, $aspects);
				$this->assertSame($model, $aspects->getAspectsModel());

				return $ret;
			}));

			$this->assertSame($modelBefore, $asp->getAspectsModel());

		}

		public function testResetAspects() {

			$model = new TestModel();


			$model->withAspects(function (TestModelAspects $aspects) use ($model, &$aspectsCall1) {
				$aspectsCall1 = $aspects;
			});

			$this->assertSame($model, $model->resetAspects());

			$model->withAspects(function (TestModelAspects $aspects) use ($model, &$aspectsCall2) {
				$aspectsCall2 = $aspects;
			});


			$this->assertNotSame($aspectsCall1, $aspectsCall2);

		}

		public function testAspectsMethodCall() {

			$model = new TestModel();

			$in = new stdClass();

			$this->assertSame($in, $model->aspectMethod($in)[0]);

		}


		public function testAspectsMethodCall_isForwardedToSameInstance() {

			$model = new TestModel();

			$in = new stdClass();

			$this->assertSame(1, $model->aspectMethod($in)[1]);
			$this->assertSame(2, $model->aspectMethod($in)[1]);

		}

		public function testNotAspectMethodCall() {

			$model = new TestModel();


			$this->expectException(BadMethodCallException::class);

			$model->notAnAspectMethod();

		}

		public function testNotAspectMethodCall_parentHasNo__callMethod() {

			$model = new TestModelWithParent();


			$this->expectException(BadMethodCallException::class);

			$model->notAnAspectMethod();

		}

		public function testNotAspectMethodCall_parentHas__callMethod() {

			$model = new TestModelWithParentAndCallHandler();

			$this->assertTrue($model->notAnAspectMethod());

		}

		public function testWithAspectMocks() {

			$model = new TestModel();

			$res = null;
			TestModel::withAspectMocks(function () use (&$res, $model) {
				$res = $model->aspectMethod(9, 10);
			}, [
				'aspectMethod' => function ($m, $i, $j) use ($model) {
					$this->assertSame($model, $m);

					return $i + $j;
				}
			]);

			$this->assertSame(19, $res);

			// now, aspects should not be mocked anymore
			$in = new stdClass();
			$this->assertSame(1, $model->aspectMethod($in)[1]);

		}
		
		public function testWithAspectMocks_nested() {

			$model = new TestModel();

			$res = null;
			$res2 = null;
			TestModel::withAspectMocks(function () use (&$res, &$res2, $model) {
				$res = $model->aspectMethod(9, 10);

				TestModel::withAspectMocks(function () use (&$res2, $model) {
					$res2 = $model->aspectMethod(9, 10);
				}, [
					'aspectMethod' => function ($m, $i, $j) use ($model) {
						$this->assertSame($model, $m);

						return $i - $j;
					}
				]);
				
			}, [
				'aspectMethod' => function ($m, $i, $j) use ($model) {
					$this->assertSame($model, $m);

					return $i + $j;
				}
			]);
			

			$this->assertSame(19, $res);
			$this->assertSame(-1, $res2);

			// now, aspects should not be mocked anymore
			$in = new stdClass();
			$this->assertSame(1, $model->aspectMethod($in)[1]);

		}

		public function testWithAspectMocks_invalidMethodName() {

			$this->expectException(InvalidArgumentException::class);

			TestModel::withAspectMocks(function () {
			}, [
				'notImplementedMethod' => function () {}
			]);


		}

	}