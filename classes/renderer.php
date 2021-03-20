<?php

namespace block_recommenda;

abstract class renderer {
    protected $final_array;

    public function get_final_array()
    {
        return $this->final_array;
    }

    abstract public function render(array $final_array);

}