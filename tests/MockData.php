<?php

namespace Tests;

trait MockData
{
    public function dataString(string $file): string
    {
        return file_get_contents(base_path('/tests/data/' . $file));
    }

    public function dataXml(string $file)
    {
        return simplexml_load_string($this->dataString($file));
    }
}
