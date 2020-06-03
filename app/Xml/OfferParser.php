<?php

namespace App\Xml;

use XMLReader;

class OfferParser extends Parser 
{
    protected int $city_id = 0;

    function setCityId(int $id)
    {
        $this->city_id = $id;
    } 

    function city(): string
    {
        if(!$this->findNode("Классификатор"))
            return "";
        $xml = $this->xml->expand();
        $city = $this->firstContent($xml, "Наименование");
        if(preg_match('/\((.*)\)/', $city, $matches))
            $city = $matches[1];
        return $city;
    }

    function offers(): iterable
    {
        if(!$this->findNode("Предложение"))
            return;

        do {
            yield $this->parse($this->xml->expand());
        } while($this->xml->next("Предложение"));
    }

    protected function parse(\DomElement $xml): Offer
    {
        $offer = new Offer;
        $offer->city_id = $this->city_id;
        $offer->product_id = (int) $this->firstContent($xml, 'Код');  
        $offer->quantity = (int) $this->firstContent($xml, 'Количество');  
        foreach($xml->getElementsByTagName('Цена') as $el)
        {
            $offer->price = (float) $this->firstContent($el, 'ЦенаЗаЕдиницу');  
            break;
        }
        return $offer;
        
    }
} 
