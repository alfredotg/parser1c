<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Xml\ProductParser;

class ImportReaderTest extends TestCase
{
    use \Tests\MockData;

    public function testProductParser()
    {
        $xml = new \XMLReader;
        $xml->open(base_path('/tests/data/import.xml'));

        $parser = new ProductParser($xml);
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
}
