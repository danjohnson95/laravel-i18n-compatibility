# Laravel i18n Compatibility

![Tests](https://github.com/danjohnson95/laravel-i18n-compatibility/actions/workflows/test.yml/badge.svg)

This package makes Laravel's translation work in tandem with frontend localisation frameworks, such as i18next. So you can share the same translation strings between your Laravel backend, and your React or Vue frontend.

- [Laravel i18n Compatibility](#laravel-i18n-compatibility)
  - [Example](#example)
  - [Installation](#installation)
  - [Usage](#usage)

## Example

Normally, your Laravel translation strings that interpolate variables would look like this:

```json
    // resources/lang/en.json
    {
        "greeting": "Welcome, :name!"
    }
```

But with this package, you can also define your translation strings like this:

```json
    // resources/lang/en.json
    {
        "greeting": "Welcome, {{name}}!"
    }
```

Meaning you can share that same translation string with your frontend localisation framework.

It works for pluralisation too. Laravel usually expects pluralisation strings to be delimited by a pipe character, like this:

```json
    // resources/lang/en.json
    {
        "apples": "There is one apple|There are many apples"
    }
```

But with this package, you can also define your pluralisation strings like this:

```json
    // resources/lang/en.json
    {
        "apples_one": "There is one apple",
        "apples_other": "There are many apples"
    }
```

It's completely backwards compatible with Laravel's syntax too, so your Laravel application will consume either syntax.

## Installation

You can install the package via composer:

```bash
composer require danjohnson95/laravel-i18n-compatibility
```

## Usage

In Laravel, you can use your localisation strings exactly as you would normally.

```json
    // resources/lang/en.json
    {
        "greeting": "Welcome, {{name}}!",
        "apples_one": "There is one apple",
        "apples_other": "There are many apples"
    }
```

```php
    // app/Http/Controllers/ExampleController.php
    public function index()
    {
        return response()->json([
            'greeting' => __('greeting', ['name' => 'Dan']),
            'apples' => trans_choice('apples', ['count' => 1]),
        ]);
    }
```
