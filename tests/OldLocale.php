<?php

use Dialect\TransEdit\Models\Locale;

class OldLocale extends \Dialect\TransEdit\TestCase
{
    /** @test */
    public function it_adds_en_to_locales_on_migrate()
    {
        $this->assertDatabaseHas('transedit_locales', [
            'name' => 'en',
        ]);
    }

    /** @test */
    public function it_can_add_new_locale_and_get_the_new_model_back()
    {
        $this->assertNotNull(transEdit()->addLocale('sv'));
        $this->assertDatabaseHas('transedit_locales', [
            'name' => 'sv',
        ]);
    }

    /** @test */
    public function it_cant_add_existing_locale_but_still_gets_a_model_back()
    {
        transEdit()->addLocale('sv');
        $this->assertNotNull(transEdit()->addLocale('sv'));
        $this->assertCount(2, Locale::all());
    }

    /** @test */
    public function it_can_get_list_of_locales()
    {
        transEdit()->addLocale('sv');
        $this->assertCount(2, transEdit()->getLocales());
    }

    /** @test */
    public function it_can_remove_locale()
    {
        transEdit()->addLocale('sv');
        transEdit()->removeLocale('sv');

        $this->assertDatabaseMissing('transedit_locales', [
            'name' => 'sv',
        ]);
    }
}
