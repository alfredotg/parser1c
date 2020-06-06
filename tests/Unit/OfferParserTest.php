<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Xml\OfferParser;
use App\Offer;

class OfferParserTest extends TestCase
{
    public function testParse()
    {
        $parser = new OfferParser(base_path('/tests/data/offer.xml'));
        $this->assertEquals($parser->city(), 'Санкт-Петербург');
        $city_id = 100;
        $parser->setCityId($city_id);
        $offers = [];
        foreach ($parser->offers() as $offer) {
            $offers[] = $offer;
        }
        $offer = array_shift($offers);
        $this->assertEquals($offer->city_id, $city_id);
        $this->assertEquals($offer->product_id, 408808);
        $this->assertEquals($offer->quantity, 25);

        $offer = array_shift($offers);
        $this->assertEquals($offer->product_id, 305549);
    }
}
