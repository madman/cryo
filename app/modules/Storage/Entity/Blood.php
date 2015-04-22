<?php

namespace Storage\Entity;

use Core\ApplicationRegistry;
use Core\Db\Entity;

class Blood extends Entity {
    
    const GENDER_MALE   = 'm';
    const GENDER_FAMALE = 'f';
    
    const GROUP_1 = 1; //0(I)
    const GROUP_2 = 2; //A(II) 
    const GROUP_3 = 3; //B(III)
    const GROUP_4 = 4; //AB(IV)
    
    const RH_MINUS  = 0; // Rh-
    const RH_PLUS   = 1;  // Rh-
    
    protected $code;
    protected $gender;
    protected $blood_group;
    protected $rh;
    protected $is_check_mother_blood = false;
    protected $jadk;
    protected $viability;
    protected $volume;
    protected $blood_count = 0;
    
    public function extract() {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'rh' => $this->rh,
            'is_check_mother_blood' => $this->is_check_mother_blood,
            'jadk' => $this->jadk,
            'viability' => $this->viability,
            'volume' => $this->volume,
            'blood_count' => $this->blood_count,
        ];
    }
    public function hydrate($data) {
        foreach ($data as $property=>$value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }
    
    public static function genderList() {
        return [
            self::GENDER_MALE => 'Чоловіча',
            self::GENDER_FAMALE => 'Жіноча',
        ];
    }
    
    public static function groupList() {
        return [
            self::GROUP_1 => '0(I)',
            self::GROUP_2 => 'A(II)',
            self::GROUP_3 => 'B(III)',
            self::GROUP_4 => 'AB(IV)',
        ];
    }
    
    public static function rhList() {
        return [
            self::RH_MINUS  => 'Rh(-)',
            self::RH_PLUS   => 'Rh(+)',
        ];
    }
    
}