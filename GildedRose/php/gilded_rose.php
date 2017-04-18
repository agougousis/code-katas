<?php

abstract class ItemUpdater
{
    protected $item;

    protected $maxQuality;

    protected $minQuality;

    public function __construct(Item $item, $maxQuality = 50, $minQuality = 0)
    {
        $this->maxQuality = $maxQuality;
        $this->minQuality = $minQuality;

        $this->item = $item;

        $this->updateSellIn();
    }

    protected function updateSellIn()
    {
        $this->item->sell_in--;
    }

    abstract protected function getQualityDiff();

    abstract function update();
}

class NormalUpdater extends ItemUpdater
{
    public function update()
    {
        $this->item->quality = max($this->minQuality, ($this->item->quality+$this->getQualityDiff()));
    }

    protected function getQualityDiff()
    {
        return ($this->item->sell_in < 0)? -2 : -1;
    }
}

class SulfurasUpdater extends ItemUpdater
{
    public function update()
    {
        // Never decreases in quality
    }

    protected function updateSellIn()
    {
        // SellIn does not update for this type of items.
    }

    protected function getQualityDiff()
    {
        // The quality does not change
    }
}

class BackstageUpdater extends ItemUpdater
{
    public function update()
    {
        $this->item->quality = min($this->maxQuality, ($this->item->quality+$this->getQualityDiff()));
    }

    protected function getQualityDiff()
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
        $this->item->quality = min($this->maxQuality, ($this->item->quality+$this->getQualityDiff()));
    }

    protected function getQualityDiff()
    {
        return 1;
    }
}

class ConjuredUpdater extends ItemUpdater
{
    public function update()
    {
        $this->item->quality = max($this->minQuality, ($this->item->quality+$this->getQualityDiff()));
    }

    protected function getQualityDiff()
    {
        return ($this->item->sell_in < 0)? -4 : -2;
    }
}


class GildedRose {

    private $items;

    private $specialUpdaters = [
        'Aged Brie'         =>  'AgedBrieUpdater',
        'Sulfuras'          =>  'SulfurasUpdater',
        'Backstage passes'  =>  'BackstageUpdater',
        'Conjured'          =>  'ConjuredUpdater'
    ];

    function __construct($items) {
        $this->items = $items;
    }

    function update_quality()
    {
        foreach ($this->items as &$item) {
            // If we were in PHP 7 :
            // $updaterClass = $this->specialUpdaters[$item->name] ?? 'NormalUpdater' ;
            $updaterClass = isset($this->specialUpdaters[$item->name]) ? $this->specialUpdaters[$item->name] : 'NormalUpdater' ;

            $updater = new $updaterClass($item);

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

