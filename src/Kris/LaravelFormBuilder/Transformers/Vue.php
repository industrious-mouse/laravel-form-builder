<?php

namespace Kris\LaravelFormBuilder\Transformers;

use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Form;

class Vue
{
    /**
     * Transform Form into a VueFormGenerator Schema
     *
     * @see https://vue-generators.gitbook.io/vue-generators/
     *
     * @param \Kris\LaravelFormBuilder\Form $form
     *
     * @return array
     * @throws \ReflectionException
     */
    public function transform(Form $form)
    {
        $data = [];

        foreach ($form->getFields() as $field) {
            $data[] = $this->map($field);
        }

        return array_filter($data);
    }

    public function extractValues(Form $form)
    {
        $data = [];

        foreach ($form->getFields() as $field) {
            if($this->getValue($field)){
                $data[$field->getRealName()] = $this->getValue($field);
            }
        }

        return array_filter($data);
    }

    protected function getValue(FormField $field)
    {
        return $field->getOption('value');
    }

    /**
     * Map Field into correct format
     *
     * @param \Kris\LaravelFormBuilder\Fields\FormField $field
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function map(FormField $field)
    {
        //Return if unsupported type
        if(!$this->getType($field)) {
            return null;
        }

        return array_merge([
            'type' => $this->getType($field),
            'inputType' => $field->getType(),
            'label' => $field->getOption('label'),
            'validator' => 'validateInput'
        ], $this->additionalMapping($field));
    }

    /**
     * Help differentiate between different FormFields
     *
     * @param \Kris\LaravelFormBuilder\Fields\FormField $field
     *
     * @return string
     * @throws \ReflectionException
     */
    protected function getType(FormField $field)
    {
        $instance = class_basename($field);
        switch ($instance) {
            case 'CheckableType':
                return $this->handleCheckableTypes($field);
                break;

            case 'SelectType':
                return $this->handleSelectTypes($field);
                break;

            case 'InputType':
                return $this->handleInputTypes($field);
                break;

            case 'TextareaType':
                return 'textArea';
                break;

            case 'StaticType':
                return 'text';
                break;

            default:
                return null;
                break;
        }
    }

    protected function handleCheckableTypes($field)
    {
        if($field->getType() === 'radio') {
            return 'radios';
        }

        return 'checkbox';
    }

    protected function handleSelectTypes($field)
    {
        if($field->getOption('multiple')) {
            return 'vueMultiSelect';
        }

        return 'select';
    }

    protected function handleInputTypes($field)
    {
        if($field->getType() === 'date') {
            return 'date-picker';
        }

        if($field->getType() === 'file') {
            return 'file-upload';
        }

        return 'input';
    }

    /**
     * Method to add additional attributes to field definition
     *
     * @param \Kris\LaravelFormBuilder\Fields\FormField $field
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function additionalMapping(FormField $field)
    {
        $data = [];

        if($this->getType($field) !== 'button'){
            $data['model'] = $field->getRealName();
        }

        if ($field->getDefaultValue()) {
            $data['default'] = $field->getDefaultValue();
        }

        if ($field->getOption('choices')) {
            $values = collect($field->getOption('choices'))
                ->map(function($item, $key) {
                    if(is_array($item)){
                        return $item;
                    }

                    return [
                        'id' => $key,
                        'name' => $item
                    ];
                })
                ->values();

            $data['values'] = $values;
        }

        if ($field->getOption('attributes')) {
            $data['attributes'] = $field->getOption('attributes');
        }

        if ($field->getOption('multiple')) {
            $data['selectOptions'] = [
                'multiple' => $field->getOption('multiple'),
                'trackBy' => 'id',
                'label' => 'name',
            ];
        }

        if($field->getOption('noneSelectedText')) {
            $data['noneSelectedText'] = $field->getOption('noneSelectedText');
        }

        if ($field->getOption('styleClasses')) {
            $data['styleClasses'] = $field->getOption('styleClasses');
        }

        if($field->getOption('required')) {
            $data['required'] = $field->getOption('required');
        }

        if($field->getOption('placeholder')) {
            $data['placeholder'] = $field->getOption('placeholder');
        }

        if($field->getOption('hint')) {
            $data['hint'] = $field->getOption('hint');
        }

        if($field->getOption('min')) {
            $data['min'] = $field->getOption('min');
        }

        if($field->getOption('max')) {
            $data['max'] = $field->getOption('max');
        }

        if($field->getOption('rows')) {
            $data['rows'] = $field->getOption('rows');
        }

        if($field->getOption('disabled')) {
            $data['disabled'] = $field->getOption('disabled');
        }

        if($field->getOption('options')) {
            $data['options'] = $field->getOption('options');
        }

        return $data;
    }
}
