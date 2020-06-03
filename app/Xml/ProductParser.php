<?php

namespace App\Xml;

use XMLReader;

class ProductParser
{
    protected XMLReader $xml;

    function __construct(XMLReader $xml)
    {
        $this->xml = $xml;
    }

    function products(): iterable
    {
        while($this->xml->name != "Товар" && $this->xml->read()) {
            continue;
        };

        do {
            yield $this->parseProduct($this->xml->expand());
        } while($this->xml->next('Товар'));
    }

    protected function firstContent(\DomElement $xml, string $name): string
    {
        return $xml->getElementsByTagName($name)->item(0)->textContent;
    } 

    protected function parseProduct(\DomElement $xml): Product
    {
        $product = new Product;
        $product->id = (int) $this->firstContent($xml, 'Код');
        $product->name = strval($this->firstContent($xml, 'Наименование'));
        $product->weight = $this->firstContent($xml, 'Вес');
        $usages = [];
        foreach($xml->getElementsByTagName('Взаимозаменяемости') as $els)
        {
            foreach($els->getElementsByTagName('Взаимозаменяемость') as $usage)
            {
                $data = [];
                foreach(['Марка', 'Модель', 'КатегорияТС'] as $field)
                    $data[] = $this->firstContent($usage, $field);
                $usages[] = implode('-', $data);
            }
        }
        $product->usage = implode('|', $usages);
        return $product;
        
    }
} 
