<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\BulkWriter;
use App\Xml\ProductParser;
use App\Product;

class ParseProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:product {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse and save products xml.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = $this->argument('file');
        $xml = new \XMLReader;
        $xml->open($file);
        $parser = new ProductParser($xml);

        $writer = new BulkWriter(1000, new Product());
        $writer->on_save = function(int $count) {
            printf("%d items saved\n", $count);
        };
        foreach($parser->products() as $product)
            $writer->add(get_object_vars($product));
        $writer->save();
    }
}
