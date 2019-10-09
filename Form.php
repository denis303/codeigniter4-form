<?php

namespace denis303\codeigniter4;

class Form
{

    protected $_model;

    protected $_errors = [];

    protected $errorTemplate = '<div class="error">{error}</div>';

    protected $groupTemplate = '<div class="form-group">{label}{input}{error}</div>';

    public function __construct(object $model, array $errors = [])
    {
        $this->_model = $model;

        $this->_errors = $errors;

        helper(['form']);
    }

    public function renderGroup($data, $name, $content, array $options = [])
    {
        if (array_key_exists('template', $options))
        {
            $template = $options['template'];

            unset($options['template']);
        }
        else
        {
            $template = $this->groupTemplate;
        }

        $options['{input}'] = $content;

        $options['{label}'] = $this->getLabel($options);

        $error = $this->getArrayValue($options. 'error');

        $options['{error}'] = $this->renderError($error);

        return strtr($template, $options);
    }

    public function renderError($error)
    {
        if (!$error)
        {
            return '';
        }

        return strtr($this->errorTemplate, ['{error}' => $error]);
    }

    public function renderErrors($errors = [], $renderAllErrors = true)
    {
        $return = '';

        if ($renderAllErrors)
        {
            $errors = array_merge($this->_errors, $errors);
        }

        foreach($errors as $error)
        {
            $return .= $this->renderError($error);
        }

        return $return;
    }

    public function setErrors($errors, $merge = false)
    {
        if ($merge)
        {
            $errors = array_merge($this->_errors, $errors);
        }

        $this->_errors = $errors;
    }

    protected function _getValue($data, $name, $default = null)
    {
        if (is_array($data))
        {
            if (array_key_exists($name, $data))
            {
                return $data[$name]
            }
        }
        else
        {
            if (property_exists($data, $name))
            {
                return $data->$name;
            }
        }

        return $default;
    }

    protected function getName(array $options, $default = null)
    {
        return $this->_getValue($options, 'name', $default);
    }

    protected function getLabel(array $options, $default = null)
    {
        return $this->_getValue($options, 'label', $default);
    }

    protected function getValue($data, $name, $default = '')
    {
        return $this->_getValue($data, $name, $default);
    }

    public function getError(string $field, $default = null)
    {
        return $this->_getValue($this->_errors, $field, $default);
    }

    public function open($action = null, $attributes = [], array $hidden = []): string
    {
        return form_open($action, $attributes, $hidden);
    }

    public function openMultipart($action = null, $attributes = [], array $hidden = []): string
    {
        return form_open_multipart($action, $attributes, $hidden);
    }

    public function hidden($data, $name, bool $recursing = false, array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_hidden($name, $value, $recursing);
    }

    public function input($data, $name, array $attributes = []): string
    {
        if (array_key_exists('type', $attributes))
        {
            $type = $attributes['type'];

            unset($attributes['type']);
        }
        else
        {
            $type = null;
        }

        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_input($name, $value, $attributes, $type);
    }

    public function inputGroup($data, $name, array $attributes = [], array $groupOptions = [])
    {
        $input = $this->input($data, $name, $attributes);

        return $this->renderGroup($input, $groupOptions);
    }

    public function password($data, $name, array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_password($name, $value, $attributes);
    }

    public function upload($data, $name, array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_upload($name, $value, $attributes);
    }

    public function textarea($data, $name, array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_textarea($name, $value, $attributes);
    }

    public function multiselect($data, $name, array $list = [], array $value = [], array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_multiselect($name, $list, $value, $attributes);
    }

    public function dropdown($data, $name, $list = [], $value = [], arrat $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_dropdown($name, $list, $value, $attributes);
    }

    public function checkbox($data, $name, string $value = '', bool $checked = false, array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_checkbox($name, $value, $checked, $attributes);
    }

    public function radio($data, $name, string $value = '', bool $checked = false, array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_radio($name, $value, $checked, $attributes);
    }

    public function submit($name = '', string $value = '', array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_submit($name, $value, $attributes);
    }

    public function reset($name = '', string $value = '', array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_reset($name, $value, $attributes);
    }

    public function button($name = '', string $content = '', array $attributes = []): string
    {
        $name = $this->getName($name, $attributes);

        return form_button($name, $content, $attributes);
    }
  
    public function label(string $label_text = '', string $id = '', array $attributes = []): string
    {
        return form_label($label_text, $id, $attributes);
    }

    public function datalist(string $name, string $value = '', array $attributes = []): string
    {
        $value = $this->getValue($data, $name);

        $name = $this->getName($attributes, $name);

        return form_datalist($name, $value, $attributes);
    }

    public function fieldset(string $legend_text = '', array $attributes = []): string
    {
        return form_fieldset($legend_text, $attributes);
    }

    public function fieldsetClose(string $extra = ''): string
    {
        return form_fieldset_close($extra);
    }

    public function close(string $extra = ''): string
    {
        return form_close($extra);
    }

    public function setValue(string $field, string $default = '', bool $html_escape = true): string
    {
        return set_value($field, $default, $html_escape);
    }

    public function setSelect(string $field, string $value = '', bool $default = false): string
    {
        return set_select($field, $value, $default);
    }

    public function setCheckbox(string $field, string $value = '', bool $default = false): string
    {
        return set_checkbox($field, $value, $default);
    }

    public function setRadio(string $field, string $value = '', bool $default = false): string
    {
        return set_radio($field, $value, $default);
    }

}