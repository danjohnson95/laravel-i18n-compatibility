<?php

namespace DanJohnson95\LaravelI18nCompatibility\Tests\SetLocale;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class BaseFunctionalityTest extends TestCase
{
    use WithWorkbench;

    public function setUp(): void
    {
        parent::setUp();

        app()->setLocale('es');
    }

    public function test_it_returns_the_string_for_the_given_language()
    {
        $this->assertEquals(
            'Este es un solo string',
            __('laravel_standard_test.single_string'),
        );
    }

    public function test_it_interpolates_strings()
    {
        $this->assertEquals(
            'Este es un string que contiene un variable',
            __('laravel_standard_test.string_with_variable', [
                'var' => 'un variable',
            ]),
        );
    }

    public function test_it_handles_pluralisation()
    {
        $this->assertEquals(
            'Hay 1 prueba',
            trans_choice('laravel_standard_test.pluralisation', 1),
        );

        $this->assertEquals(
            'Hay 2 pruebas',
            trans_choice('laravel_standard_test.pluralisation', 2)
        );
    }

    public function test_it_handles_pluralisation_with_interpolation()
    {
        $this->assertEquals(
            'Hay una prueba',
            trans_choice('laravel_standard_test.pluralisation_with_interpolation', 1, [
                'value' => 'una',
            ])
        );

        $this->assertEquals(
            'Hay dos pruebas',
            trans_choice('laravel_standard_test.pluralisation_with_interpolation', 2, [
                'value' => 'dos',
            ])
        );
    }
}
