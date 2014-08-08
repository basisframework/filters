<?php
require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/fixtures/DummyObject.php';

class FilterProxyTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		$this->age = 10;
		$this->dummy = new DummyObject($this->age);
		$this->filter = new Basis\Filters\FilterProxy($this->dummy);
	}

	public function testFilterWithoutMethodsBehavesLikeProxiedObject() {
		$this->assertEquals($this->age, $this->filter->getAge());
		$this->assertEquals($this->age, $this->filter->age);

		$new_age = 5;
		$this->filter->setAge($new_age);
		$this->assertEquals($new_age, $this->dummy->getAge());
		$this->assertEquals($new_age, $this->dummy->age);
		$this->assertEquals($new_age, $this->filter->getAge());
		$this->assertEquals($new_age, $this->filter->age);

		$new_age = 7;
		$this->filter->age = $new_age;
		$this->assertEquals($new_age, $this->dummy->getAge());
		$this->assertEquals($new_age, $this->dummy->age);
		$this->assertEquals($new_age, $this->filter->getAge());
		$this->assertEquals($new_age, $this->filter->age);
	}

	public function testHookingAnArgumentFreeMethod() {
		$this->filter->hook('getAge', function($filter) {
			return $filter->next() * 2;
		});

		$this->assertEquals($this->age, $this->dummy->getAge());
		$this->assertEquals($this->age * 2, $this->filter->getAge());
	}

	public function testHookingAMethodWithArguments() {
		$this->filter->hook('setAge', function($filter, $age) {
			return $filter->next($age * 2);
		});

		$new_age = 3;
		$this->filter->setAge($new_age);
		$this->assertEquals($new_age * 2, $this->dummy->getAge());
		$this->assertEquals($new_age * 2, $this->dummy->age);
		$this->assertEquals($new_age * 2, $this->filter->getAge());
		$this->assertEquals($new_age * 2, $this->filter->age);
	}

	public function testHookingAGetter() {
		$this->filter->hook('get:age', function($filter) {
			return $filter->next() * 2;
		});

		$this->assertEquals($this->age, $this->dummy->getAge());
		$this->assertEquals($this->age, $this->dummy->age);
		$this->assertEquals($this->age, $this->filter->getAge());
		$this->assertEquals($this->age * 2, $this->filter->age);
	}

	public function testHookingASetter() {
		$this->filter->hook('set:age', function($filter, $val) {
			$filter->next($val * 2);
		});

		$new_age = 2;
		$this->filter->age = $new_age;
		$this->assertEquals($new_age * 2, $this->dummy->getAge());
		$this->assertEquals($new_age * 2, $this->dummy->age);
		$this->assertEquals($new_age * 2, $this->filter->getAge());
		$this->assertEquals($new_age * 2, $this->filter->age);
	}

};
