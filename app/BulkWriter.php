<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BulkWriter
{
    public $on_save;
    protected int $chunk_size;
    protected array $models = [];
    protected array $prepared = [];

    function __construct(int $chunk_size)
    {
        $this->chunk_size = $chunk_size;
    }

    public function size(): int
    {
        return count($this->models);
    }

    public function add(Model $model)
    {
        $this->models[] = $model;
        if(count($this->models) >= $this->chunk_size)
            $this->save();
    }

    public function save(): void
    {
        if(count($this->models) == 0)
            return;
        list($sql, $bindigs) = $this->prepare();
        $this->execute($sql, $bindigs);
        if($this->on_save !== null)
        {
            $on_save = $this->on_save;
            $on_save(count($this->models));
        }
        $this->models = [];
    }

    protected function execute(string $sql, array $bindigs): void
    {
        DB::statement($sql, $bindigs);
        //$pdo = DB::connection()->getPdo();
        //if(!isset($this->prepared[$sql]))
        //{
        //    $sttm = $pdo->prepare($sql);
        //    if(!$sttm)
        //        throw new \Exception($pdo->errorInfo());
        //    $this->prepared[$sql] = $sttm;
        //}
        //$sttm = $this->prepared[$sql];
        //if(!$sttm->execute($bindigs))
        //    throw new \Exception($sttm->errorInfo());
    }

    public function prepare(): array
    {
        $bindigs = [];
        $fields = false;
        foreach($this->models as $model)
        {
            if(!$fields)
                $fields = array_keys($model->attributesToArray());
            $bindigs = array_merge($bindigs, array_values($model->attributesToArray()));
        }

        $sql = 'INSERT INTO `'.$model->getTable().'` (`'.implode('`, `', $fields).'`) values ';
                                        
        $questions = array_pad([], count($fields), '?');
        $placements = '('.implode(', ', $questions).')';

        $sql .= implode(',', array_pad([], count($this->models), $placements));

        $updates = [];
        foreach($fields as $field)
            if($field != 'id')
                $updates[] = '`'.$field.'`=VALUES(`'.$field.'`)'; 
        if($updates)
        {
            $sql .= ' ON DUPLICATE KEY UPDATE ';
            $sql .= implode(', ', $updates);
        }
        return [$sql, $bindigs];
    }
}
