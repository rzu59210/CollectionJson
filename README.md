# Collection Json

PHP implementation of the Collection+JSON Media Type

Specification: 
- [http://amundsen.com/media-types/collection/](http://amundsen.com/media-types/collection/)

## Installation

CollectionJson requires php >= 5.5

Install CollectionJson with [Composer](https://getcomposer.org/)

```json
{
    "require": {
        "mvieira/json-collection": "dev-master"
    }
}
```

## Contributing

```sh
$ git clone git@github.com:mickaelvieira/CollectionJson.git
$ cd CollectionJson
$ composer install
```

### Run the test

The test suite has been written with [PHPSpec](http://phpspec.net/)

```sh
$ ./bin/phpspec run
```

### PHP Code Sniffer

This project follows the coding style guide [PSR2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

```sh
$ ./bin/phpcs --standard=PSR2 ./src/
```

## Documentation

### Creating a collection

```php
use CollectionJson\Entity\Collection;
use CollectionJson\Entity\Item;

$collection = new Collection();

$item = new Item();
$item->setHref('http://example.com/item/1');

$collection->addItem($item);

print json_encode($collection);
```

```json
{
    "collection": {
        "version": "1.0",
        "items": [
            {
                "href": "http://example.com/item/1"
            }
        ]
    }
}
```

### Printing the data

Note: Apart from the data's value property, which allows having a NULL value (see. [specification](http://amundsen.com/media-types/collection/format/#properties-value)), All ```NULL``` properties and empty arrays will be excluded from the JSON and Array representation.

#### Printing a JSON representation

All entities implement the [JsonSerializable](http://php.net/manual/en/class.jsonserializable.php) interface,
you can therefore call at any time the method ```json_encode()```.

```php
print json_encode($collection);
```

```json
{
    "collection": {
        "version": "1.0",
        "items": [
            ...
        ],
        "links": [
            ...
        ]
    }
}
```

#### Printing an Array representation

All entities implement a custom interface named ```ArrayConvertible```, so you can call at any time the method ```toArray()```.
This method will be called recursively on all nested entities.

```php
print_r($collection->toArray());
```

```php
Array
(
    [collection] => Array
        (
            [version] => 1.0
            [items] => Array
                ...

            [links] => Array
                ...

        )

)
```

#### Wrapping

The ```CollectionJson\Entity\Collection``` entity will be wrapped...

```php
echo json_encode($collection);
```

```json
{
    "collection": {
        "version": "1.0"
     }
}
```
...however others entities will not be wrapped when they are converted in a JSON or an Array.

```php
$template = new Template();
echo json_encode($template);
```

```json
{
    "data": [
        ...
    ]
}
```

But you can wrap the json or the array representation by calling the method ```wrap()```

```php
$template->wrap('template');
echo json_encode($template);
```

```json
{
    "template": {
        "data": [
            ...
        ]
    }
}
```
### Creating an entity

All entities can be created by using the static method ```fromArray```...

```php
$data = Data::fromArray([
    'name' => 'email',
    'value' => 'email value'
]);
```

...or by using the accessors.

```php
$data = new Data();
$next->setName('email');
$next->setValue('email value');
```

#### Collection

[http://amundsen.com/media-types/collection/format/#objects-collection](http://amundsen.com/media-types/collection/format/#objects-collection)

```php
use CollectionJson\Entity\Collection;
use CollectionJson\Entity\Item;
use CollectionJson\Entity\Query;
use CollectionJson\Entity\Error;
use CollectionJson\Entity\Template;
use CollectionJson\Entity\Link;

$collection = new Collection();
$collection->setHref('http://www.example.com');

$collection->addItem(new Item());
$collection->addItemSet([
    new Item(),
    new Item()
]);

$collection->addLink(new Link());
$collection->addLinkSet([
    new Link(),
    new Link()
]);

$collection->addQuery(new Query());
$collection->addQuerySet([
    new Query(),
    new Query()
]);

$collection->setError(new Error());
$collection->setTemplate(new Template());
```


Build the collection from a existing JSON, which means you can use the library as client API

```php
$json = '
{
    "collection": {
        "version": "1.0",
        "href": "http://example.org/friends/",
        "links": [
            {
                "rel": "feed",
                "href": "http://example.org/friends/rss"
            }
        ]
    }
}';

$collection = $this::fromJson($json);
$link = $collection->getLinksSet()[0];
echo $link->getRel();  // feed
echo $link->getHref(); // http://example.org/friends/rss
```

#### Item

[http://amundsen.com/media-types/collection/format/#arrays-items](http://amundsen.com/media-types/collection/format/#arrays-items)

```php
use CollectionJson\Entity\Item;
use CollectionJson\Entity\Data;
use CollectionJson\Entity\Link;

$item = new Item();
$item->setHref('http://www.example.com');

$item->addData(new Data());
$item->addDataSet([
    new Data(),
    new Data()
]);

$item->addLink(new Link());
$item->addLinkSet([
    new Link(),
    new Link()
]);

```

#### Link

[http://amundsen.com/media-types/collection/format/#arrays-links](http://amundsen.com/media-types/collection/format/#arrays-links)

```php
use CollectionJson\Entity\Link;
use CollectionJson\Entity\Type\Render;
use CollectionJson\Entity\Type\Relation;

$link = new Link();
$link->setName('link name');
$link->setHref('http://www.example.com');
$link->setPrompt('prompt value');
$link->setRel(Relation::ITEM);
$link->setRender(Render::IMAGE); // default Render::LINK
```

#### Query

[http://amundsen.com/media-types/collection/format/#arrays-queries](http://amundsen.com/media-types/collection/format/#arrays-queries)

```php
use CollectionJson\Entity\Query;
use CollectionJson\Entity\Data;
use CollectionJson\Entity\Type\Relation;

$query = new Query();
$query->setHref('http://www.example.com');
$query->setRel(Relation::SEARCH);
$query->setName('value');
$query->setPrompt('value');

$query->addData(new Data());
$query->addDataSet([
    new Data(),
    new Data()
]);
```

#### Error

[http://amundsen.com/media-types/collection/format/#objects-error](http://amundsen.com/media-types/collection/format/#objects-error)

```php
use CollectionJson\Entity\Error;

$error = new Error();
$error->setTitle('error title');
$error->setCode('error code');
$error->setMessage('error message');
```

#### Template

[http://amundsen.com/media-types/collection/format/#objects-template](http://amundsen.com/media-types/collection/format/#objects-template)

```php
use CollectionJson\Entity\Template;
use CollectionJson\Entity\Data;

$template = new Template();

$template->addData(new Data());
$template->addDataSet([
    new Data(),
    new Data()
]);

```

#### Data

[http://amundsen.com/media-types/collection/format/#arrays-data](http://amundsen.com/media-types/collection/format/#arrays-data)

```php
use CollectionJson\Entity\Data;
use CollectionJson\Entity\ListData;
use CollectionJson\Entity\Option;
use CollectionJson\Entity\Type\Input;

$data = new Data();
$data->setName('data name');
$data->setPrompt('data prompt');
$data->setValue('data value');
```

### Working with data and links

In order to work with CollectionJson Arrays [Data](http://amundsen.com/media-types/collection/format/#arrays-data), [Links](http://amundsen.com/media-types/collection/format/#arrays-links), the API provides 2 interfaces that implement the same logic.

- The interface ```DataAware``` implemented by ```Item```, ```Query``` and ```Template``` entities,
provides the methods ```addData```, ```addDataSet```, ```getDataSet```, ```getFirstData``` and ```getLastData```
- The interface ```LinkAware``` implemented by ```Collection``` and ```Item``` entities,
provides the methods```addLink```, ```addLinkSet```, ```getLinkSet```, ```getFirstLink``` and ```getLastLink```

They allows you to add the corresponding entities to objects that implement them.

```php
$item = new Item();

// this...
$item->addData([
    'name' => 'email',
    'value' => 'email value'
]);

// ...is similar to 
$data = Data::fromArray([
    'name' => 'email',
    'value' => 'email value'
]);
$item->addData($data);

// and that...
$item->addDataSet([
    [
        'name' => 'email',
        'value' => 'email value'
    ],
    [
        'name' => 'tel',
        'value' => 'tel value'
    ]
]);

// ...is similar to 
$data1 = Data::fromArray([
    'name' => 'email',
    'value' => 'email value'
]);
$data2 = Data::fromArray([
    'name' => 'tel',
    'value' => 'tel value'
]);
$item->addDataSet([
    $data1,
    $data2
]);
```

