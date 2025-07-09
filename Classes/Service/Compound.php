<?php

namespace AyhanKoyun\IupacNomenclature\Service;
abstract class Compound {
    protected $chainLength;
    protected $substituents = [];

    public function __construct($chainLength, $substituents) {
        $this->chainLength = $chainLength;
        $this->substituents = $substituents;
    }

    abstract public function getName();

    protected function formatName($name) {
        return ucfirst(strtolower($name));
    }
}

