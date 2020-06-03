<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\BulkWriter;
use App\Xml\OfferParser;
use App\Offer;
use App\City;

class ParseOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:offer {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse and save offers xml.';

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
        $parser = new OfferParser($this->argument('file'));

        $city_name = $parser->city();
        if(!$city_name)
            throw new \Exception('Not found city name');
        $city = City::firstOrCreate(['name' => $city_name]);
        $parser->setCityId($city->id);

        $writer = new BulkWriter(1000, new Offer());
        $writer->on_save = function(int $count) {
            printf("%d offers saved\n", $count);
        };
        foreach($parser->offers() as $product)
            $writer->add(get_object_vars($product));
        $writer->save();
    }
}
