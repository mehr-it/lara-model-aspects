<?php


	namespace MehrItLaraModelAspectsTests\Model;


	use MehrIt\LaraModelAspects\ModelAspects;

	class TestModelWithParent extends TestModelWithParent__parent
	{
		use ModelAspects;
	}

	class TestModelWithParent__parent
	{

	}