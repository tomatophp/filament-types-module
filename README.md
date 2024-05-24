# Filament Types Manager Module

[![Latest Stable Version](https://poser.pugx.org/tomatophp/filament-types-module/version.svg)](https://packagist.org/packages/tomatophp/filament-types-module)
[![PHP Version Require](http://poser.pugx.org/tomatophp/filament-types-module/require/php)](https://packagist.org/packages/tomatophp/filament-types-module)
[![License](https://poser.pugx.org/tomatophp/filament-types-module/license.svg)](https://packagist.org/packages/tomatophp/filament-types-module)
[![Downloads](https://poser.pugx.org/tomatophp/filament-types-module/d/total.svg)](https://packagist.org/packages/tomatophp/filament-types-module)

Manage any type on your app in Database with easy to use Resource for FilamentPHP

## Screenshots

![Types](https://github.com/tomatophp/filament-types-module/blob/master/arts/types.png)
![Filters](https://github.com/tomatophp/filament-types-module/blob/master/arts/filters.png)
![Type Col](https://github.com/tomatophp/filament-types-module/blob/master/arts/type-col.png)
![Form](https://github.com/tomatophp/filament-types-module/blob/master/arts/form.png)

## Installation

```bash
composer require tomatophp/filament-types-module-module
```
after install your package please run this command

```bash
php artisan filament-types:install
```

## Register New Type

you can add new type using config file `config/filament-types.php` or you can register a type from your provider using our Facade

```php
use Modules\TypesManager\Facades\FilamentTypes;

FilamentTypes::register([
    'types',
    'groups'
], 'accounts');
```

## Use Type Helper

you can access types from anywhere by using type helper function

```php
type_of($key);
```

i will return type object

## Use Type Column

you can use type column in your table like this

```php
use Modules\TypesManager\Components\TypeColumn;

TypeColumn::make('type')->searchable(),
```

## Support

you can join our discord server to get support [TomatoPHP](https://discord.gg/Xqmt35Uh)

## Docs

you can check docs of this package on [Docs](https://docs.tomatophp.com/plugins/laravel-package-generator)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

Please see [SECURITY](SECURITY.md) for more information about security.

## Credits

- [Fady Mondy](mailto:info@3x1.io)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
