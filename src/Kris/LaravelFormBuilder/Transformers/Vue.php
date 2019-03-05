<?php

namespace Kris\LaravelFormBuilder\Transformers;

use Kris\LaravelFormBuilder\Fields\FormField;
use Kris\LaravelFormBuilder\Form;

class Vue
{
    public function transform(Form $form)
    {
        $data = [];

        //        dd($form->getFields());

        foreach ($form->getFields() as $field) {
            $data[] = $this->map($field);
        }

        return $data;
    }

    protected function map(FormField $field)
    {
        return array_merge([
          'type' => $this->getType($field),
          'inputType' => $field->getType(),
          'label' => $field->getOption('label'),
          'model' => $field->getRealName(),
        ], $this->additionalMapping($field));
    }

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

    protected function additionalMapping(FormField $field)
    {
        $data = [];

        if ($field->getValue()) {
            $data['value'] = $field->getValue();
        }

        if ($field->getDefaultValue()) {
            $data['default'] = $field->getDefaultValue();
        }

        //        TODO: Add these to core
        //        if($field->getRequired()) {
        //            $data['required'] = $field->getRequired();
        //        }
        //
        //        if($field->getPlaceholder()) {
        //            $data['placeholder'] = $field->getPlaceholder();
        //        }
        //
        //        if($field->getHint()) {
        //            $data['hint'] = $field->getHint();
        //        }

        return $data;
    }
}
