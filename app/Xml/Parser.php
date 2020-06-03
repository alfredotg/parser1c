<?php

namespace App\Xml;

use XMLReader;

class Parser
{
    protected XMLReader $xml;

    function __construct(string $file)
    {
        $xml = new \XMLReader;
        $xml->open($file);
        $this->xml = $xml;
    }

    protected function firstContent(\DomElement $xml, string $name): string
    {
        return $xml->getElementsByTagName($name)->item(0)->textContent;
    } 

    protected function findNode(string $name): bool
    {
        while($this->xml->name != $name && $this->xml->read()) 
            continue;
        return $this->xml->name == $name;
    }
}

