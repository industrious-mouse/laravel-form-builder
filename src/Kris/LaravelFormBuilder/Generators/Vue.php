<?php

namespace Kris\LaravelFormBuilder\Generators;

use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\Transformers;

class Vue
{
    /** @var Transformers\Vue  */
    protected $transformer;

    /** @var FormBuilder  */
    protected $builder;

    /** @var Form  */
    protected $form;

    /** @var array  */
    protected $fields = [];

    /** @var array  */
    protected $model = [];

    /** @var array  */
    protected $options = [];

    /**
     * Vue constructor.
     */
    public function __construct()
    {
        $this->transformer = resolve(Transformers\Vue::class);
        $this->builder = resolve(FormBuilder::class);
    }

    /**
     * Set Form Attribute
     *
     * @param $class
     * @param $name
     *
     * @return $this
     */
    public function setForm($class, $name)
    {
        $this->form = $this->builder->create(title_case($class), [], [
            'name' => camel_case($name)
        ]);

        return $this;
    }

    /**
     * Invoke transformer to generate a schema
     *
     * @return $this
     * @throws \ReflectionException
     */
    public function transform()
    {
        $this->fields = $this->transformer->transform($this->form);

        return $this;
    }

    /**
     * Set Model Attribute
     *
     * @param array $model
     *
     * @return $this
     */
    public function setModel(array $model = [])
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set Options Attribute
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get raw values
     *
     * @return array
     */
    public function raw()
    {
        return [
            'schema' => [
                'fields' => $this->fields
            ],
            'model' => (object) $this->model,
            'options' => (object) $this->options
        ];
    }

    /**
     * Get raw values in Json compatible format
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->raw());
    }
}
