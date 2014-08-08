<?php

class DummyObject {
	private $age;

	public function __construct($age) {
		$this->age = $age;
	}

	public function __get($name) {
		return $this->$name;
	}

	public function __set($name, $val) {
		$this->$name = $val;
	}

	public function setAge($age) {
		$this->age = $age;
	}

	public function getAge() {
		return $this->age;
	}
}