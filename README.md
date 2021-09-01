# Installation

## Pre-requisites

- PHP >= 7.2.5
- yarn or npm
- [composer](https://getcomposer.org/download/)
- the [symfony cli](https://symfony.com/download)

## Installing PHP dependencies

```
$ composer install
```

## Installing javascript dependencies

```
$ yarn install
```

## Creating javascript bundles

For development run

```
$ yarn run dev
```

For production run

```
$ yarn run build
```

## Setting up the database

The default database configuration can be found in the `.env` file, under the
`DATABASE_URL` variable.

Migration
```
php bin/console doctrine:migrations:migrate
```
## Launching a local server for development

```
$ symfony server:start
```
