<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Xml\ProductParser;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\BulkWriter;
use App\Product;

class InsertTest extends TestCase
{
    use RefreshDatabase;

    public function testBulkInsert()
    {
        $product = new Product();
        $product->id = 10;
        $product->name = 'Product name';
        $product->weight = 3.4;
        $product->usage = 'Some|Some';

        $product2 = clone($product);
        $product2->id = 12;

        $writer = new BulkWriter(2);
        $writer->add($product);
        $this->assertEquals(1, $writer->size());
        $writer->add($product2);
        $this->assertEquals(0, $writer->size());

        $loaded = Product::findOrFail($product->id);
        $this->assertEquals($loaded->name, $product->name);
    }
}
