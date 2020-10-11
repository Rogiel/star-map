# Star Map
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FRogiel%2Fstar-map.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2FRogiel%2Fstar-map?ref=badge_shield)


This library allows you to read StarCraft II map files from PHP.

A object-oriented API is provided to browse through the metadata and the minimap image.

## Features
* Read .SC2Map files from all public game versions
* **Minimap**: Allows to read the embeded minimap image

## Installation

The recommended way of installing this library is using Composer.

    composer require "rogiel/star-map"
    
This library uses [php-mpq](https://github.com/Rogiel/php-mpq) to parse and extract compressed information inside the map file.
    
## Example

```php
use Rogiel\StarMap\Map;

// Parse the map
$map = new Map('Ruins of Seras.SC2Map');

// Get the map name in multiple locales
$documentHeader = $map->getDocumentHeader();

echo sprintf('Map name (English): %s', $documentHeader->getName()).PHP_EOL; // english is default
echo sprintf('Map name (French): %s', $documentHeader->getName('frFR')).PHP_EOL;

// Get the map size
$mapInfo = $map->getMapInfo();
$x = $mapInfo->getWidth();
$y = $mapInfo->getHeight();
echo sprintf('Map size: %sx%s', $x, $y).PHP_EOL;

// Export Minimap image as a PNG
$map->getMinimap()->toPNG('Minimap.png');
```

The output to the snippet above is the following:

```
Map name (English): Ruins of Seras
Map name (French): Ruines de Seras
Map size: 224x192
```

Have fun!

## License
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FRogiel%2Fstar-map.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2FRogiel%2Fstar-map?ref=badge_large)