<?php

namespace Kris\LaravelFormBuilder\Generators;

use Illuminate\Database\Eloquent\Model;
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
    protected $fields;

    /** @var array  */
    protected $model;

    /** @var array  */
    protected $options;

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
     * @param array $options
     *
     * @return $this
     */
    public function setModel(array $options = [])
    {
        if(!isset($options['model']) || !isset($options['id'])){
            return $this;
        }

        $instance = $this->resolveModelInstance($options['model']);
        $model = $this->getModelInstance($instance, $options['id']);

        //TODO: Check if user has access to instance else return empty
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

    protected function invoke()
    {
        $this->mergeOptions();
        $this->trimModelInstance();
    }

    /**
     * Get raw values
     *
     * @return array
     */
    public function raw()
    {
        $this->invoke();

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
        return json_encode($this->raw(), JSON_HEX_QUOT|JSON_HEX_TAG);
    }

    /**
     * Merge form-generator and user passed options
     *
     * @return $this
     */
    protected function mergeOptions()
    {
        $this->options = array_merge($this->options, $this->form->getFormOptions());

        return $this;
    }

    /**
     * Trim to only return model values required
     *
     * @return $this
     */
    protected function trimModelInstance()
    {
        $fields = array_keys($this->form->getFields());
        $this->model = collect($this->model)->only($fields);

        return $this;
    }

    /**
     * Returns an instance of model
     *
     * @param $model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function resolveModelInstance($model)
    {
        $class = config('laravel-form-builder.default_model_namespace', 'App') . '\\' . title_case($model);

        if (!class_exists($class)) {
            throw new \InvalidArgumentException('Model class with name ' . $class . ' does not exist.');
        }

        return resolve($class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $instance
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function getModelInstance(Model $instance, $id)
    {
        return $instance->findOrFail($id);
    }
}
