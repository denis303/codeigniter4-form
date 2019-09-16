<?php

namespace denis303\codeigniter4;

class Form
{

    protected $_model;

    protected $_errors = [];

    public $containerTemplate = 'form_input_container';

    public $errorsTemplate = 'form_errors';

    public function __construct(object $model)
    {
        $this->_model = $model;

        $this->_errors = (array) $this->_model->errors();

        helper(['form']);
    }

    public function renderErrors($errors = [])
    {
        $errors = array_merge($this->_errors, $errors);

        return view($this->errorsTemplate, [
            'errors' => $errors,
            'form' => $this
        ], ['saveData' => false]);
    }

    public function setErrors($errors)
    {
        $this->_errors = $errors;
    }

    public function addErrors($errors)
    {
        $this->_errors = array_merge($this->_errors, $errors);
    }

    public function generateId($field)
    {
        $class = get_class($this->_model);

        $segments = explode("\\", $class);

        $segments = array_reverse($segments);

        $first = array_shift($segments);

        $return = $first . '-' . $field;

        $return = strtolower($return);

        return $return;
    }

    public function renderContainer(string $field, string $input, array $options = []) : string
    {
        $options['label'] = $this->getLabel($field);

        $options['error'] = $this->getError($field);

        $options['input'] = $input;

        if (!array_key_exists('labelOptions', $options))
        {
            $options['labelOptions'] = [];
        }

        return view($this->containerTemplate, $options, ['saveData' => false]);
    }

    public function getLabel(string $field)
    {
        if (method_exists($this->_model, 'getFieldLabel'))
        {
            return $this->_model->getFieldLabel($field);
        }

        $rules = $this->_model->getValidationRules();

        if (array_key_exists($field, $rules))
        {
            if (is_array($rules[$field]) && array_key_exists('label', $rules[$field]))
            {
                return $rules[$field]['label'];
            }
        }

        return $field;
    }

    public function getValue($data, string $field, $default = '')
    {
        if (is_array($data))
        {
            if (array_key_exists($field, $data))
            {
                return $data[$field];
            }

            return $default;
        }

        return !empty($data->$field) ? $data->$field : $default;
    }

    public function getError(string $field)
    {
        return array_key_exists($field, $this->_errors) ? $this->_errors[$field] : null;
    }

    public function input($data, string $field, array $options = [], array $containerOptions = []) : string
    {
        return $this->renderContainer(
            $field, 
            form_input($field, $this->getValue($data, $field), $options), 
            $containerOptions
        );
    }

    public function password($data, string $field, array $options = [], array $containerOptions = []) : string
    {
        return $this->renderContainer(
            $field, 
            form_password($field, $this->getValue($data, $field), $options), 
            $containerOptions
        );
    }

    public function textarea($data, string $field, array $options = [], array $containerOptions = []) : string
    {
        return $this->renderContainer(
            $field, 
            form_textarea($field, $this->getValue($data, $field), $options),
            $containerOptions
        );
    }

    public function checkbox($data, string $field, array $options = [], array $containerOptions = []) : string
    {
        if (empty($options['id']))
        {
            $options['id'] = $this->generateId($field);
        }

        if (empty($containerOptions['labelOptions']['for']))
        {
            $containerOptions['labelOptions']['for'] = $options['id'];
        }

        $content = '<br>';

        $content .= form_hidden($field, 0);

        $content .= form_checkbox($field, 1, $this->getValue($data, $field) ? true : false, $options);

        return $this->renderContainer(
            $field, 
            $content,
            $containerOptions
        );
    }

}