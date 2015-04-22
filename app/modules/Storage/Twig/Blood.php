<?php

namespace Storage\Twig;

use Storage\Entity\Blood as BloodEntity;

class Blood extends \Twig_Extension{

    public function getName(){
        return 'blood';
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('gender', [$this, 'gender']),
            new \Twig_SimpleFunction('blood_group', [$this, 'bloodGroup']),
            new \Twig_SimpleFunction('blood_rh', [$this, 'bloodRh']),
            new \Twig_SimpleFunction('is_check_mother_blood', [$this, 'isChecked'], ['is_safe' => ['html']]),
        ];
    }

    public function gender($gender) {
        $list = BloodEntity::genderList();

        if (array_key_exists($gender, $list)) {
            return $list[$gender];
        } else {
            return 'N/a';
        }
    }

    public function bloodGroup($group) {
        $list = BloodEntity::groupList();

        if (array_key_exists($group, $list)) {
            return $list[$group];
        } else {
            return 'N/a';
        }
    }

    public function bloodRh($rh) {
        $list = BloodEntity::rhList();

        if (array_key_exists($rh, $list)) {
            return $list[$rh];
        } else {
            return 'N/a';
        }
    }

    public function isChecked($status) {
        if (1 == $status) {
            return '<p class="text-success">Так</p>';
        } elseif (0 == $status) {
            return '<p class="text-warning">Ні</p>';
        } else {
            return '<p class="text-muted">N/a</p>';
        }
    }
}
