<?php

namespace Storage\Entity;

use Core\ApplicationRegistry;
use Core\Db\Entity;

class Blood extends Entity {
    
    const GENDER_MALE = 'm';
    const GENDER_FAMALE = 'f';
    
    protected $id = null;
    protected $gender;
    protected $is_check_mother_blood = false;
    protected $jadk;
    protected $viability;
    protected $volume;
    protected $count = 0;
    
    public function extract() {
        return [
            'id' => $this->id,
            'gender' => $this->gender,
            'is_check_mother_blood' => $this->is_check_mother_blood,
            'jadk' => $this->jadk,
            'viability' => $this->viability,
            'volume' => $this->volume,
            'count' => $this->count,
        ];
    }
    public function hydrate($data) {
        foreach ($data as $property=>$value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }
    
}