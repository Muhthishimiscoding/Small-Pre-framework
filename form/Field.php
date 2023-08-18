<?php
namespace MuhthishimisCoding\PreFramework\form;

use MuhthishimisCoding\PreFramework\Model;

class Field
{
    public Model $model;
    public function __construct(Model &$model)
    {
        $this->model = $model;
    }
    public function input(
        $name,
        $label,
        $type = 'text',
        $classes = '',
        $placeholder = '',
        $attribute ='',
        $html =
        '<div class="mb-3">
            <label class="form-label">%s</label>
            <input type="%s" name="%s" value="%s" class="form-control %s %s" %s %s>
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
            $attribute,
            $placeholder,
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
}