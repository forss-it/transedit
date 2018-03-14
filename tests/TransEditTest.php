<?php

class TransEditTest extends \Dialect\TransEdit\TestCase
{
    /** @test */
    public function it_can_set_key()
    {
        $key = str_random(5);
        $val = str_random(6);
        transEdit()->setKey($key, $val);

        $this->assertDatabaseHas('transedit_keys', [
            'name' => $key,
        ]);
        $this->assertDatabaseHas('transedit_translations', [
            'locale_id' => 1,
            'key_id' => 1,
            'value' => $val,
        ]);
    }

    /** @test */
    public function it_creates_the_locale_if_missing_when_setting_key()
    {
        $locale = str_random(2);
        $key = str_random(5);
        $val = str_random(6);
        transEdit()->locale($locale)->setKey($key, $val);

        $this->assertDatabaseHas('transedit_locales', [
            'name' => $locale,
        ]);
    }

    /** @test */
    public function it_can_use_shortcut_to_set_key()
    {
        $key = str_random(5);
        $val = str_random(6);
        transEdit()->locale('sv')->key($key, $val);

        $this->assertDatabaseHas('transedit_keys', [
            'name' => $key,
        ]);
        $this->assertDatabaseHas('transedit_translations', [
            'locale_id' => 2,
            'key_id' => 1,
            'value' => $val,
        ]);
    }

    /** @test */
    public function it_can_get_key()
    {
        $key = str_random(5);
        $val = str_random(6);
        transEdit()->setKey($key, $val);
        $this->assertEquals(transEdit()->getKey($key), $val);
    }

    /** @test */
    public function it_returns_key_name_if_no_value_was_found()
    {
        $key = str_random(5);
        $this->assertEquals(transEdit()->getKey($key), $key);
    }

    /** @test */
    public function it_can_use_shortcut_to_get_key()
    {
        $key = str_random(5);
        $val = str_random(6);
        transEdit()->key($key, $val);
        $this->assertEquals(transEdit()->key($key), $val);
    }

    /** @test */
    public function it_can_get_different_locales_for_the_same_key()
    {
        $key = str_random(5);
        $val1 = str_random(6);
        $val2 = str_random(7);
        $locale = str_random(3);
        transEdit()->key($key, $val1);
        transEdit()->locale($locale)->key($key, $val2);

        $this->assertEquals(transEdit()->key($key), $val1);
        $this->assertEquals(transEdit()->locale($locale)->key($key), $val2);
    }

    /** @test */
    public function it_returns_vuecomponent_instead_of_text_if_edit_mode_is_on()
    {
        $key = str_random(5);
        $val = str_random(6);

        transEdit()->key($key, $val);
        $transEdit = new \Dialect\TransEdit\TransEdit(true);

        $this->assertEquals(get_class($transEdit->key($key)), 'Illuminate\Support\HtmlString');
    }

    /** @test */
    public function it_can_enable_edit_mode()
    {
        transEdit()->enableEditMode();
        $this->assertTrue(session('transedit-edit-mode-on'));
    }

    /** @test */
    public function it_can_disable_edit_mode()
    {
        transEdit()->disableEditMode();
        $this->assertFalse(session('transedit-edit-mode-on'));
    }

    /** @test */
    public function it_can_change_current_locale()
    {
        transEdit()->setCurrentLocale('en');
        $this->assertEquals(session('transedit-current-locale'), 'en');
    }

    /** @test */
    public function it_can_set_locale_language_name()
    {
        $locale = str_random(2);
        $name = str_random(10);
        transEdit()->setLocaleLanguageName($locale, $name);

        $this->assertDatabaseHas('transedit_locales', [
            'name' => $locale,
            'language' => $name,
        ]);
    }

    /** @test */
    public function it_can_get_all_translations_for_key()
    {
        $locale1 = str_random(2);
        $locale2 = str_random(3);
        $key = str_random(4);
        $value1 = str_random(3);
        $value2 = str_random(4);

        transEdit()->locale($locale1)->setKey($key, $value1);
        transEdit()->locale($locale2)->setKey($key, $value2);
        $translations = transEdit()->getAllTranslationsForKey($key);

        $this->assertCount(3, $translations);
        $this->assertContains($value1, $translations);
        $this->assertContains($value2, $translations);
        $this->assertContains($key, $translations);
    }

    /** @test */
    public function it_uses_fallback_locale_if_no_translation_is_found()
    {
        config()->set('transedit.fallback_locale', 'en');

        $key = str_random(4);
        $value = str_random(5);
        transEdit()->locale('en')->setKey($key, $value);
        $this->assertEquals(transEdit()->locale('foo')->getKey($key), $value);
    }
}
