<?php
class HelperTest extends  \Dialect\TransEdit\TestCase {
	/** @test */
	public function helper_functions_returns_instance_of_TestEdit(){
		$this->assertEquals(get_class(transEdit()), 'Dialect\TransEdit\TransEdit');
	}

	/** @test */
	public function it_can_get_key_directly_with_helper_function(){
		$key = str_random(5);
		$val = str_random(6);
		transEdit()->setKey($key, $val);

		$this->assertEquals($val, transEdit($key));
	}

	/** @test */
	public function it_can_set_key_directly_with_helper_function(){
		$key = str_random(5);
		$val = str_random(6);
		transEdit($key, $val);

		$this->assertEquals($val, transEdit($key));
	}
}