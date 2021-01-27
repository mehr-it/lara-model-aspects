<?php


	namespace MehrItLaraModelAspectsTests\Model\Aspects;


	use MehrIt\LaraModelAspects\AbstractModelAspects;
	use MehrItLaraModelAspectsTests\Model\TestModel;
	use PHPUnit\Framework\ExpectationFailedException;

	class TestModelAspects extends AbstractModelAspects
	{
		public $callCount = 0;

		public function getAspectsModel() {
			return $this->model;
		}

		public function setAspectModel($model) {

			$this->setModel($model);

			return $this;
		}

		public function aspectMethod($in) {

			$this->assertModelSet();

			if (!($this->model instanceof TestModel))
				throw new ExpectationFailedException('Aspect method must be called with model as first parameter');

			return [$in, ++$this->callCount];
		}

		public function invokeAssertModelSet() {
			$this->assertModelSet();
		}
	}