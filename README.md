# Shopware Psysh
----

![showcase](https://media.giphy.com/media/lMqlDUn389q7P31kti/source.gif)

Inspired by the `frosh tinker` that inspired by `laravel tinker` command from laravel this plugin adds a similar command to Shopware 6. 

It's basically a fork version of `frosh tinker` for Shopware 6 with additional features.

Read more:
 
- [`frosh/tinker`](https://github.com/FriendsOfShopware/FroshTinker) repository
- [`bobthecow/psysh`](https://github.com/bobthecow/psysh) repository

## New command:

```
bin/console sw:psysh
```

- Enter `ls` to get list of scoped variables.
- Enter `list` to get list of avaialbe commands.

## Additional features:
- Shopware's Core Services aliases 
(e.g new EqualsFilter via CLI instead of fully qualified class name `Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter`.
_(There is some rare cases that two classes use the same class alias so you need to give the fullname to make it work)_

- Shopware's service container auto-completion.
![alias and auto suggest](https://i.imgur.com/xhK4QIy.png)



- Struct Object caster.
![Struct object caster](https://i.imgur.com/C9pUnOy.png)

- Default scoped variables: $container (Service Container object), $connection (Doctrine Connection object), $context (Default context object), $criteria (default criteria object)...and more

## Requirements

- Shopware 6.3 or above (older versions might work, but were not tested)
- PHP 7.1 or above

## Installation via composer

```
composer require vin-sw/psysh
bin/console plugin:refresh
bin/console plugin:install --activate ShopwarePsysh
bin/console sw:psysh
```

## Contributing

Feel free to fork and send pull requests!


## Licence

This project uses the [MIT License](LICENCE.md).
