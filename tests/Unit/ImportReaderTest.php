<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Xml\ProductParser;
use App\Xml\OfferParser;

class ImportReaderTest extends TestCase
{
    use \Tests\MockData;

    public function testProductParser()
    {
        $parser = new ProductParser(base_path('/tests/data/import.xml'));
        $products = [];
        foreach($parser->products() as $product)
            $products[] = $product;

        $product = array_shift($products);
        $this->assertEquals(420575, $product->id);
        $this->assertEquals(
            'Поддон картера двиг. (ISF3.8) R {ПАЗ} V масла=6.5-8.0L, 1 масл.канал, без отверстия для подогревателя, 5257821 "CM"', 
            $product->name);
        $this->assertEquals(0, $product->weight);
        $this->assertEquals('', $product->usage);

        $product = array_shift($products);
        $this->assertEquals(408784, $product->id);
        $this->assertEquals(2.745, $product->weight);
        $this->assertEquals('CUMMINS-ISBe6.7 (ISDe6.7)-Двигатели', $product->usage);
    }

    public function testOfferParser()
    {
        $parser = new OfferParser(base_path('/tests/data/offer.xml'));
        $this->assertEquals($parser->city(), 'Санкт-Петербург');
        $city_id = 100;
        $parser->setCityId($city_id);
        $offers = [];
        foreach($parser->offers() as $offer)
            $offers[] = $offer;
        $offer = array_shift($offers);
        $this->assertEquals($offer->city_id, $city_id);
        $this->assertEquals($offer->product_id, 408808);
        $this->assertEquals($offer->quantity, 25);

        $offer = array_shift($offers);
        $this->assertEquals($offer->product_id, 305549);
    }
}
