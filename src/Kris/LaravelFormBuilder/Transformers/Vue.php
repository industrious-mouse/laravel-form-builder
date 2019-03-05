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

        return $data;
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
        return array_merge([
          'type' => $this->getType($field),
          'inputType' => $field->getType(),
          'label' => $field->getOption('label'),
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
        $instance = (new \ReflectionClass($field))->getShortName();
        switch ($instance) {
          case 'InputType':
            return 'input';
            break;

          case 'SelectType':
            return 'select';
            break;

          case 'CheckableType':
            return $field->getType();
            break;

          case 'ButtonType':
            return 'button';
            break;

          default:
            return 'input';
            break;
        }
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
            $data['value'] = $field->getOption('choices');
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

        if($field->getOption('disabled')) {
            $data['disabled'] = $field->getOption('disabled');
        }

        return $data;
    }
}
