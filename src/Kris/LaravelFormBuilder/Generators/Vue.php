<?php

namespace Kris\LaravelFormBuilder\Generators;

use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\Transformers;

class Vue
{
    protected $transformer;
    protected $builder;
    protected $form;
    protected $fields;
    protected $model;
    protected $options;

    public function __construct()
    {
        $this->transformer = resolve(Transformers\Vue::class);
        $this->builder = resolve(FormBuilder::class);
    }

    public function setForm($class)
    {
        $this->form = $this->builder->create($class);

        return $this;
    }

    public function transform()
    {
        $this->fields = $this->transformer->transform($this->form);

        return $this;
    }

    public function setModel(array $model = [])
    {
        $this->model = $model;

        return $this;
    }

    public function setOptions(array $options = [])
    {
        $this->options = $options;

        return $this;
    }

    public function raw()
    {
        return [
            'fields' => $this->fields,
            'model' => $this->model,
            'options' => $this->options
        ];
    }

    public function toJson()
    {
        return json_encode($this->raw());
    }
}
