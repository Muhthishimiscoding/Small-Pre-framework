<?php
namespace Worth\core\form;

use Worth\core\Model;
use Worth\core\form\Field;

class Form
{

    public static function begin(Model $model, string $method = 'post',string $action = '', string $attributes =''){
       echo "<form method='$method' action='$action' $attributes >";
       return new Field($model);
    }
    public static function end(){
        echo '</form>';
    }
    // public function field(Model $model, $fieldType='input', $attributes){
    //     return new Field($model,$attributes);
    // }
}
