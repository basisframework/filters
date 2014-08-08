<?php
namespace Basis\Filters;

class Filter {
	private $filter;
	private $next;

	public function __construct(callable $filter, Filter $next = NULL) {
		$this->filter = $filter;
		$this->next = $next;
	}

	public function call($args = array()) {
		if($this->next) {
			array_unshift($args, $this);
		}
		
		return call_user_func_array($this->filter, $args);
	}

	public function next() {
		return $this->next->call(func_get_args());
	}
	
};
