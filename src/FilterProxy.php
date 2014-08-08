<?php
namespace Basis\Filters;
use Basis\Objects\Proxy;
use Basis\Filters\Filter;
use RuntimeException;

class FilterProxyException extends RuntimeException {};

class FilterProxy extends Proxy {
	static $VALID_HOOK_TYPES = array(
		'get' => 'getters',
		'set' => 'setters',
	);

	private $getters = array();
	private $setters = array();
	private $methods = array();

	public function __get($name) {
		if(isset($this->getters[$name])) {
			return $this->getters[$name]->call();

		} else {
			return $this->getTarget()->$name;
		}
	}

	public function __set($name, $val) {
		if(isset($this->setters[$name])) {
			$this->setters[$name]->call(array($val));

		} else {
			$this->getTarget()->$name = $val;
		}
	}

	public function __call($fn, $args) {
		if(isset($this->methods[$fn])) {
			return $this->methods[$fn]->call($args);

		} else {
			return call_user_func_array(array($this->getTarget(), $fn), $args);
		}
	}

	public function hook($fn, callable $filter) {
		$dest = 'methods';

		// Support for other hook types by type:target syntax
		if(strpos($fn, ':') !== FALSE) {
			$fn = explode(':', $fn);
			if(!isset(self::$VALID_HOOK_TYPES[$fn[0]])) {
				throw new FilterProxyException(sprintf('Could not add hook to method of type "%s"', $fn[0]));
			}

			$dest = self::$VALID_HOOK_TYPES[$fn[0]];
			$fn = $fn[1];
		}

		if(isset($this->{$dest}[$fn])) {
			$this->{$dest}[$fn] = new Filter($filter, $this->{$dest}[$fn]);

		} else {
			switch($dest) {
			case 'methods':
				$this->methods[$fn] = new Filter($filter, new Filter(array($this->getTarget(), $fn)));
				break;

			case 'getters':
				$target = $this->getTarget();
				$this->getters[$fn] = new Filter($filter, new Filter(function() use($target, $fn) {
					return $target->$fn;
				}));
				break;

			case 'getters':
				$target = $this->getTarget();
				$this->getters[$fn] = new Filter($filter, new Filter(function($val) use($target, $fn) {
					$target->$fn = $val;
				}));
				break;
			}
		}
	}
	
};
