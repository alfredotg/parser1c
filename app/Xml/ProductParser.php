<?php

namespace App\Xml;

use XMLReader;

class ProductParser extends Parser
{
    function products(): iterable
    {
        if(!$this->findNode("Товар"))
            return;

        do {
            yield $this->parse($this->xml->expand());
        } while($this->xml->next('Товар'));
    }

    protected function parse(\DomElement $xml): Product
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
