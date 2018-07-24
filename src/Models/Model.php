<?php

namespace MakeWeb\WHM\Models;

abstract class Model
{
    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }
}
