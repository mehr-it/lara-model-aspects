<?php


	namespace MehrItLaraModelAspectsTests\Model;


	use MehrIt\LaraModelAspects\ModelAspects;
	use MehrItLaraModelAspectsTests\Model\Aspects\TestModelAspects;

	/**
	 * Class TestModel
	 * @package MehrItLaraModelAspectsTests\Model
	 * @mixin TestModelAspects
	 */
	class TestModel
	{
		use ModelAspects;
	}