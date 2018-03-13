<?php
class ControllerTest extends  \Dialect\TransEdit\TestCase {
	/** @test */
	public function it_can_set_key_using_controller() {
		$key = str_random(4);
		$val = str_random(5);
		$this->post('/transedit/setkey', [
			'key' => $key,
			'val' => $val,
		]);

		$this->assertDatabaseHas('transedit_translations', ['value' => $val]);

	}

	/** @test */
	public function it_can_get_locales_using_controller() {

		$this->get('/transedit/locales')->assertSee('en');


	}
}
