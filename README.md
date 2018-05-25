"A PHP class, which is able to resolve dependencies (ie classes, URLs, HTML scripts .. ). You feed the class with the known dependencies of the objects you are providing and it generates an array with the objects you provided now arranged in the correct order, which is defined by the dependencies of the specific objects"


## Installation

This project assumes you have composer installed. Simply add:

"require" : {

    "rstoetter/cdependencymanager-php" : ">=1.0.0"

}

to your composer.json, and then you can simply install with:

composer install

## Namespace

Use the namespace cDependencyManager in order to access the classes provided by the repository cdependencymanager-php

## More information

See the [project wiki of cdependencymanager-php](https://github.com/rstoetter/cdependencymanager-php/wiki) for more technical informations.


