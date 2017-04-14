<?php

abstract class ItemUpdater
{
    protected $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
        $this->item->sell_in--;
    }

    abstract function update();
}

class NormalUpdater extends ItemUpdater
{
    public function update()
    {
        $this->item->quality = max(0, ($this->item->quality-$this->getQualityDrop()));
    }

    protected function getQualityDrop()
    {
        return ($this->item->sell_in < 0)? 2 : 1;
    }
}

class SulfurasUpdater extends ItemUpdater
{
    public function update()
    {
        // Never decreases in quality
    }
}

class BackstageUpdater extends ItemUpdater
{
    public function update()
    {
        $this->item->quality = min(50, ($this->item->quality+$this->getQualityIncr()));
    }

    protected function getQualityIncr()
    {
        if ($this->item->sell_in < 0){
            return -$this->item->quality; // quality should drop to zero after concert
        } elseif ($this->item->sell_in <= 5) {
            return 3;
        } elseif ($this->item->sell_in <= 10) {
            return 2;
        } else {
            return 1;
        }
    }
}

class AgedBrieUpdater extends ItemUpdater
{
    public function update()
    {
        $this->item->quality = min(50, ($this->item->quality+$this->getQualityIncr()));
    }

    protected function getQualityIncr()
    {
        return 1;
    }
}

class ConjuredUpdater extends ItemUpdater
{
    public function update()
    {
        $this->item->quality = max(0, ($this->item->quality-$this->getQualityDrop()));
    }

    protected function getQualityDrop()
    {
        return ($this->item->sell_in < 0)? 4 : 2;
    }
}


class GildedRose {

    private $items;

    function __construct($items) {
        $this->items = $items;
    }

    function update_quality()
    {
        foreach ($this->items as &$item) {
            switch ($item->name) {
                case 'Aged Brie':
                    $updater = new AgedBrieUpdater($item);
                    break;
                case 'Sulfuras':
                    $updater = new SulfurasUpdater($item);
                    break;
                case 'Backstage passes':
                    $updater = new BackstageUpdater($item);
                    break;
                case 'Conjured':
                    $updater = new ConjuredUpdater($item);
                    break;
                default:
                    $updater = new NormalUpdater($item);
            }

            $updater->update();
        }
    }
}

class Item {

    public $name;
    public $sell_in;
    public $quality;

    function __construct($name, $sell_in, $quality) {
        $this->name = $name;
        $this->sell_in = $sell_in;
        $this->quality = $quality;
    }

    public function __toString() {
        return "{$this->name}, {$this->sell_in}, {$this->quality}";
    }

}

