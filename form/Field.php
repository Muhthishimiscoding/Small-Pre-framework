<?php
namespace MuhthishimisCoding\PreFramework\form;

use MuhthishimisCoding\PreFramework\Model;

class Field
{
    public Model $model;
    // public string $type;
    // public const TYPE_TEXT='text';
    // public const TYPE_PASSWORD='password';
    // public const TYPE_NUMBER = 'number';
    public function __construct(Model &$model)
    {
        // $this->type = self::TYPE_TEXT;
        $this->model = $model;
    }
    public function input(
        $name,
        $label,
        $type = 'text',
        $classes = '',
        $html =
        '<div class="mb-3">
            <label class="form-label">%s</label>
            <input type="%s" name="%s" value="%s" class="form-control %s %s">
            %s
        </div>'
    ) {
        $bool = $this->model->hasError($name);
        echo sprintf(
            $html,
            $label,
            $type,
            $name,
            $this->model->{$name},
            $classes,
            $bool ? 'is-invalid' : '',
            $bool ? '<div class="invalid-feedback">' . $this->model->getError($name) . '</div>' : ''
        );
    }

    public function textArea(
        $name,
        $label,
        $placeholder = 'Enter Your message',
        $cols = "3",
        $rows = "6",
        $classes = '',
        $html =
        '<div class="mb-3">
             <label class="form-label">%s</label>
             <textarea name="%s" cols="%s" rows="%s" class="form-control border %s %s" placeholder="%s">%s</textarea>
            %s
        </div>'
    ) {
        $bool = $this->model->hasError($name);
        echo sprintf(
            $html,
            $label,
            $name,
            $cols,
            $rows,
            $classes,
            $bool ? 'is-invalid' : '',
            $placeholder,
            $this->model->{$name},
            $bool ? '<div class="invalid-feedback">' . $this->model->getError($name) . '</div>' : ''
        );
    }

    // public function __toString(){
    //     return '<input>';
    // }

    // public passwordType(){
    //     $this->type =self::TYPE_PASSWORD;
    //     return $this
    // }
    // When contructor function was returning input then $this would do the same with the help of __toString
}