<?php


	namespace MehrIt\LaraModelAspects\Provider;


	use Illuminate\Support\Collection;
	use Illuminate\Support\ServiceProvider;
	use MehrIt\LaraModelAspects\AbstractModelAspects;

	class LaraModelAspectsServiceProvider extends ServiceProvider
	{

		public function boot() {


			Collection::macro('setAspects', function(AbstractModelAspects $aspects) {

				foreach($this->items as $curr) {
					$curr->setAspects($aspects);
				}

				return $this;
			});


		}

	}