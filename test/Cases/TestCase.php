<?php


	namespace MehrItLaraModelAspectsTests\Cases;


	use MehrIt\LaraModelAspects\Provider\LaraModelAspectsServiceProvider;

	class TestCase extends \Orchestra\Testbench\TestCase
	{
		/**
		 * Load package service provider
		 * @param \Illuminate\Foundation\Application $app
		 * @return array
		 */
		protected function getPackageProviders($app) {


			return [
				LaraModelAspectsServiceProvider::class,
			];
		}
	}