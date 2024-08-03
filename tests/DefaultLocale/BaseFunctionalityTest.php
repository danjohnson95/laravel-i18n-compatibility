<?php

namespace DanJohnson95\LaravelI18nCompatibility\Tests\DefaultLocale;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class BaseFunctionalityTest extends TestCase
{
    use WithWorkbench;

    public function test_it_returns_the_string_for_the_given_language()
    {
        $this->assertEquals(
            'This is a single string',
            __('laravel_standard_test.single_string'),
        );
    }

    public function test_it_interpolates_strings()
    {
        $this->assertEquals(
            'This is a string that contains a variable',
            __('laravel_standard_test.string_with_variable', [
                'var' => 'a variable',
            ]),
        );
    }

    public function test_it_handles_pluralisation()
    {
        $this->assertEquals(
            'There is 1 test',
            trans_choice('laravel_standard_test.pluralisation', 1)
        );

        $this->assertEquals(
            'There are 2 tests',
            trans_choice('laravel_standard_test.pluralisation', 2)
        );
    }

    public function test_it_handles_pluralisation_with_interpolation()
    {
        $this->assertEquals(
            'There is one test',
            trans_choice('laravel_standard_test.pluralisation_with_interpolation', 1, [
                'value' => 'one',
            ])
        );

        $this->assertEquals(
            'There are two tests',
            trans_choice('laravel_standard_test.pluralisation_with_interpolation', 2, [
                'value' => 'two',
            ])
        );
    }
}
