PHP Lumen Detection
============

Simple PHP Class for detection lumen in image or RGB/Hex string

## Installation
Download detect-lumen.class.php

## Usage

### Detect lumen in image


```
<?php 
require_once 'detect-lumen.class.php'; 

$lumen = new Dropcode\Color\detectLumen('path/to/image.jpg');

echo 'Image has an average lumen of ' . $lumen->lumen();

?>
```

### Detect lumen in RGB/Hex string


```
<?php 
require_once 'detect-lumen.class.php'; 

$color = "#000";
$lumen = new Dropcode\Color\detectLumen( $color );

echo "The color {$color} has an lumen of " . $lumen->lumen();

?>
```

### Check if image or color is bright or dark


```
<?php 
require_once 'detect-lumen.class.php'; 

$lumen = new Dropcode\Color\detectLumen('path/to/image.jpg');

if( $lumen->is() == "light" ) {
	echo "Image is a light image..";
} else {
	echo "Image is a dark image..";
}

?>
```