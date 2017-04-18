<?php

require_once 'gilded_rose.php';

echo "OMGHAI!\n";

$items = array(
    new Item('+5 Dexterity Vest', 10, 20),
    new Item('Aged Brie', 2, 0),
    new Item('Elixir of the Mongoose', 5, 7),
    new Item('Sulfuras', 0, 80),
    new Item('Sulfuras', -1, 80),
    new Item('Backstage passes', 15, 20),
    new Item('Backstage passes', 10, 49),
    new Item('Backstage passes', 5, 49),
    // this conjured item does not work properly yet
    new Item('Conjured', 3, 6)
);

$app = new GildedRose($items);

$days = 3;
if (count($argv) > 1) {
    $days = (int) $argv[1];
}

for ($i = 0; $i < $days; $i++) {
    echo("-------- day $i --------\n");
    echo("name, sellIn, quality\n");
    foreach ($items as $item) {
        echo $item . PHP_EOL;
    }
    echo PHP_EOL;
    $app->update_quality();
}
