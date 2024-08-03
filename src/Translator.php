<?php

namespace DanJohnson95\LaravelI18nCompatibility;

use Illuminate\Support\Str;
use Illuminate\Translation\Translator as BaseTranslator;

class Translator extends BaseTranslator
{
    /**
     * We're overriding this so we can use the i18next syntax for pluralisation.
     * So rather than one single string with all the pluralised versions in it
     * delimited by pipes, we'll have separate keys for each pluralisation and
     * suffix the key with _zero, _one, _two, _few, _many, _other.
     */
    public function choice($key, $number, array $replace = [], $locale = null)
    {
        $replace['count'] = $number;

        // If the key exists, use that.
        if ($this->has($key)) {
            return parent::choice($key, $number, $replace, $locale);
        }

        // Otherwise, assume it's in i18next format.
        $locale = $this->localeForChoice($locale);

        // Find all the keys that relate to this one.
        $suffixes = collect([
            'zero', 'one', 'two', 'few', 'many', 'other',
        ]);

        $definedKeys = $suffixes
            ->filter(function ($suffix) use ($key) {
                return $this->has("{$key}_{$suffix}");
            })
            ->map(function ($suffix) use ($key, $replace, $locale) {
                return $this->get(
                    "{$key}_{$suffix}",
                    $replace,
                    $locale,
                );
            });

        $string = implode('|', $definedKeys->all());

        return $this->getSelector()->choose($string, $number, $locale);
    }

    /**
     * Make the place-holder replacements on a line.
     *
     * @param  string  $line
     * @param  array  $replace
     * @return string
     */
    protected function makeReplacements($line, array $replace)
    {
        if (empty($replace)) {
            return $line;
        }

        $shouldReplace = [];

        foreach ($replace as $key => $value) {
            // Support {{attribute}} syntax
            $shouldReplace['{{'.Str::ucfirst($key ?? '').'}}'] = Str::ucfirst($value ?? '');
            $shouldReplace['{{'.Str::upper($key ?? '').'}}'] = Str::upper($value ?? '');
            $shouldReplace['{{'.$key.'}}'] = $value;

            // Support Laravel's default :attribute syntax
            $shouldReplace[':'.Str::ucfirst($key ?? '')] = Str::ucfirst($value ?? '');
            $shouldReplace[':'.Str::upper($key ?? '')] = Str::upper($value ?? '');
            $shouldReplace[':'.$key] = $value;
        }

        return strtr($line, $shouldReplace);
    }

    /**
     * Add translation lines to the given locale.
     *
     * @param  array  $lines
     * @param  string  $locale
     * @param  string  $namespace
     * @return void
     */
    public function addLines(array $lines, $locale, $namespace = '*')
    {
        foreach ($lines as $key => $value) {
            $this->loaded['*']['*'][$locale][$key] = $value;
        }
    }

    /**
     * Get the translation for the given key.
     *
     * Overridden so that if a locale key isn't found, it'll fall back to English
     * rather than just outputting the locale key.
     *
     * @param  string  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @param  bool  $fallback
     * @return string|array
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;

        // For JSON translations, there is only one file per locale, so we will simply load
        // that file and then we will be ready to check the array for the key. These are
        // only one level deep so we do not need to do any fancy searching through it.
        $this->load('*', '*', $locale);

        $line = $this->loaded['*']['*'][$locale][$key] ?? null;

        if ($fallback && $line === null) {
            $this->load('*', '*', $this->fallback);
            $line = $this->loaded['*']['*'][$this->fallback][$key] ?? null;
        }

        // If we can't find a translation for the JSON key, we will attempt to translate it
        // using the typical translation file. This way developers can always just use a
        // helper such as __ instead of having to pick between trans or __ with views.
        if (! isset($line)) {
            [$namespace, $group, $item] = $this->parseKey($key);

            // Here we will get the locale that should be used for the language line. If one
            // was not passed, we will use the default locales which was given to us when
            // the translator was instantiated. Then, we can load the lines and return.
            $locales = $fallback ? $this->localeArray($locale) : [$locale];

            foreach ($locales as $locale) {
                if (! is_null($line = $this->getLine(
                    $namespace,
                    $group,
                    $locale,
                    $item,
                    $replace
                ))) {
                    return $line;
                }
            }
        }

        // If the line doesn't exist, we will return back the key which was requested as
        // that will be quick to spot in the UI if language keys are wrong or missing
        // from the application's language files. Otherwise we can return the line.
        return $this->makeReplacements($line ?: $key, $replace);
    }
}
