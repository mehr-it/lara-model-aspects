<?php


	namespace MehrIt\LaraModelAspects;


	use BadMethodCallException;
	use InvalidArgumentException;

	trait ModelAspects
	{
		/**
		 * @var string
		 */
		protected static $aspectClass;

		/**
		 * @var callable[]|null
		 */
		protected static $aspectMocks;

		/**
		 * @var AbstractModelAspects
		 */
		protected $aspects;

		/**
		 * Gets the aspects class for the current model
		 * @return string The aspects class
		 */
		protected static function aspectsClass(): string {

			if (!static::$aspectClass) {
				$segments = explode('\\', get_called_class());

				$modelClass = array_pop($segments);

				$segments[] = 'Aspects';
				$segments[] = "{$modelClass}Aspects";

				static::$aspectClass = implode('\\', $segments);
			}

			return static::$aspectClass;
		}

		/**
		 * Creates a new instance of aspects for this model
		 * @return AbstractModelAspects The new aspects instance
		 */
		public static function newAspects(): AbstractModelAspects {

			return app(static::aspectsClass());
		}

		/**
		 * Gets the aspects instance for this model
		 * @return AbstractModelAspects
		 */
		protected function aspects() {

			if (!($this->aspects ?? null))
				$this->aspects = static::newAspects();

			return $this->aspects;
		}

		/**
		 * Executes the given callback with the model aspects
		 * @param callable $callback The callback. Receives the aspects instance as first parameter.
		 * @return mixed The callback return.
		 */
		public function withAspects(callable $callback) {
			return $this->aspects()->withModel($this, $callback);
		}

		/**
		 * Sets the given aspects instance for the model
		 * @param AbstractModelAspects $aspects The aspects instance
		 * @return $this
		 */
		public function setAspects(AbstractModelAspects $aspects) {

			$aspectClass = static::aspectsClass();
			if (!($aspects instanceof $aspectClass))
				throw new InvalidArgumentException('Expected instance of ' . $aspectClass . ', got ' . get_class($aspects));

			$this->aspects = $aspects;

			return $this;
		}

		/**
		 * Resets the aspects instance for the current model
		 * @return $this
		 */
		public function resetAspects() {

			$this->aspects = null;

			return $this;
		}

		/**
		 * Executes the given callback with given aspect methods mocked
		 * @param callable $callback The callback to execute
		 * @param callable[] $mockCallbacks The mocked aspect methods. Method name as key.
		 * @return mixed The callback return
		 */
		public static function withAspectMocks(callable $callback, array $mockCallbacks) {
			$aspects = static::newAspects();
			
			$mocksBefore = self::$aspectMocks;

			try {
				foreach ($mockCallbacks as $name => $_) {
					if (!is_callable([$aspects, $name]))
						throw new InvalidArgumentException("Cannot mock aspect method " . get_class($aspects) . "::{$name}(). {$name}() is not callable.");
				}

				self::$aspectMocks = $mockCallbacks;
				
				return call_user_func($callback);
			}
			finally {
				self::$aspectMocks = $mocksBefore;
			}
		}

		/**
		 * @inheritDoc
		 */
		public function __call($name, $arguments) {

			if (self::$aspectMocks[$name] ?? null)
				return call_user_func(self::$aspectMocks[$name], $this, ...$arguments);
			
			$aspects = $this->aspects();

			if (is_callable([$aspects, $name])) {
				return $this->withAspects(function ($aspects) use ($name, $arguments) {
					return $aspects->{$name}(...$arguments);
				});
			}


			if (get_parent_class() && is_callable([parent::class, '__call']))
				return parent::__call($name, $arguments);

			throw new BadMethodCallException(sprintf(
				'Call to undefined method %s::%s()', static::class, $name
			));
		}
	}