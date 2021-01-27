<?php


	namespace MehrItLaraModelAspectsTests\Model;


	use MehrIt\LaraModelAspects\ModelAspects;

	class TestModelWithParentAndCallHandler extends TestModelWithParentAndCallHandler__parent
	{
		use ModelAspects;
	}

	class TestModelWithParentAndCallHandler__parent
	{
		public function __call($name, $arguments) {

			if ($name = 'notAnAspectMethod')
				return true;

			throw new \BadMethodCallException('Bad method called');
		}


	}