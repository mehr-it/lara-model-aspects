<?php


	namespace MehrIt\LaraModelAspects;


	use RuntimeException;

	abstract class AbstractModelAspects
	{

		protected $model;

		/**
		 * Creates a new instance
		 */
		public function __construct() {

		}

		/**
		 * Executes the given callback while setting the given model for this aspects instance
		 * @param object $model The model
		 * @param callable $callback The callback. Gets the aspects instance as first argument.
		 * @return mixed The callback return.
		 */
		public function withModel($model, callable $callback) {

			$before = $this->model;

			try {
				$this->setModel($model);

				return call_user_func($callback, $this);
			}
			finally {
				$this->model = $before;
			}

		}

		/**
		 * Sets the model
		 * @param object $model The model
		 * @return $this
		 */
		protected function setModel($model) {
			$this->model = $model;

			return $this;
		}

		/**
		 * Throws an error if the model is not set
		 */
		protected function assertModelSet(): void {

			if (!$this->model)
				throw new RuntimeException('The aspect\'s model is not set.');
		}


	}