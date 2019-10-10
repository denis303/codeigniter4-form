<?php
/**
 * @author denis303 <mail@denis303.com>
 * @license MIT
 * @link http://denis303.com
 */
namespace denis303\codeigniter4;

class Form
{

    protected $_model;

    protected $_errors = [];

    protected $errorTemplate = '<div class="error"{attributes}>{error}</div>';

    protected $labelTemplate = '<label{attributes}>{label}</label>';

    protected $groupTemplate = '<div class="form-group"{attributes}>{label}{input}</div>';

    public function __construct(object $model, array $errors = [])
    {
        $this->_model = $model;

        $this->_errors = $errors;

        helper(['form']);
    }

    public function getFieldId($data, $name, array $attributes = [])
    {
        if (array_key_exists('id', $attributes))
        {
            return $attributes['id'];
        }

        return $name . '_input';
    }

    public function getFieldName($data, $name, array $attributes = [])
    {
        if (array_key_exists('name', $attributes))
        {
            return $attributes['name'];
        }

        return $name;
    }

    public function getFieldLabel($data, $name, array $attributes = [])
    {
        if (array_key_exists('label', $attributes))
        {
            return $attributes['label'];
        }

        return $name;
    }

    public function getFieldValue($data, $name, array $attributes = [])
    {
        if (array_key_exists('value', $attributes))
        {
            return $attributes['value'];
        }

        return $this->_getValue($data, $name, '');
    }

    public function getFieldError($data, $name, array $attributes = [])
    {
        if (array_key_exists('error', $attributes))
        {
            return $attributes['error'];
        }

        $name = $this->getFieldName($data, $name, $attributes);

        if (array_key_exists($name, $this->_errors))
        {
            return $this->_errors[$name];
        }

        return null;
    }

    public function setErrors($errors, $merge = false)
    {
        if ($merge)
        {
            $errors = array_merge($this->_errors, $errors);
        }

        $this->_errors = $errors;
    }

    public function renderError($error, array $options = [])
    {
        if (!$error)
        {
            return '';
        }

        return strtr(
            $this->errorTemplate, 
            [
                '{error}' => $error,
                '{attributes}' => ''
            ]
        );
    }

    public function renderErrors($errors = [], $renderAllErrors = true, array $options = [])
    {
        $return = '';

        if ($renderAllErrors)
        {
            $errors = array_merge($this->_errors, $errors);
        }

        foreach($errors as $error)
        {
            $return .= $this->renderError($error,  $options);
        }

        return $return;
    }

    public function renderLabel($label, array $options = [])
    {
        return strtr(
            $this->labelTemplate, 
            [
                '{label}' => $label,
                '{attributes}' => stringify_attributes($options)
            ]
        );
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

        $labelOptions = [];

        if (array_key_exists('labelOptions', $options))
        {
            $labelOptions = $options['labelOptions'];
        }

        $options['{input}'] = $content;

        $options['{label}'] = $this->renderLabel($this->getFieldLabel($data, $name, $options), $labelOptions);

        $options['{error}'] = $this->renderError($this->getFieldError($data, $name, $options), $options);

        $options['{attributes}'] = '';

        return strtr($template, $options);
    }

    public function open($action = null, $attributes = [], array $hidden = []): string
    {
        return form_open($action, $attributes, $hidden);
    }

    public function openMultipart($action = null, $attributes = [], array $hidden = []): string
    {
        return form_open_multipart($action, $attributes, $hidden);
    }

    public function close(string $extra = ''): string
    {
        return form_close($extra);
    }

    public function hidden($data, $name, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        return form_hidden($name, $value);
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
            $type = 'text';
        }

        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        return form_input($name, $value, $attributes, $type);
    }

    public function inputGroup($data, $name, array $attributes = [], array $groupOptions = [])
    {
        $content = $this->input($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupOptions);
    }

    public function password($data, $name, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        return form_password($name, $value, $attributes);
    }

    public function passwordGroup($data, $name, array $attributes = [], array $groupOptions = [])
    {
        $content = $this->password($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupOptions);
    }

    public function upload($data, $name, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        return form_upload($name, $value, $attributes);
    }

    public function uploadGroup($data, $name, array $attributes = [], array $groupOptions = [])
    {
        $content = $this->upload($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupOptions);
    }

    public function textarea($data, $name, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getValue($data, $name, $attributes);

        return form_textarea($name, $value, $attributes);
    }

    public function textareaGroup($data, $name, array $attributes = [], array $groupOptions = [])
    {
        $content = $this->textarea($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupOptions);
    }

    public function multiselect($data, $name, array $list = [], array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        return form_multiselect($name, $list, $value, $attributes);
    }

    public function multiselectGroup($data, $name, $list = [], array $attributes = [], array $groupOptions = []): string
    {
        $content = $this->multiselect($data, $name, $list, $attributes);

        return $this->renderGroup($data, $name, $content, $groupOptions);
    }

    public function dropdown($data, $name, $list = [], array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        return form_dropdown($name, $list, $value, $attributes);
    }

    public function dropdownGroup($data, $name, $list = [], array $attributes = [], array $groupOptions = []): string
    {
        $content = $this->checkbox($data, $name, $list, $attributes);

        return $this->renderGroup($data, $name, $content, $groupOptions);
    }

    public function checkbox($data, $name, $value = 1, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        $currentValue = $this->getFieldValue($data, $name, $attributes);

        if (is_array($currentValue))
        {
            if (array_search($value, $currentValue) !== false)
            {
                $checked = true;
            }
            else
            {
                $checked = false;
            }
        }
        else
        {
            if ($currentValue == $value)
            {
                $checked = true;
            }
            else
            {
                $checked = false;
            }
        }

        $uncheckValue = '0';

        if (array_key_exists('uncheckValue', $attributes))
        {
            $uncheckValue = (string) $attributes['uncheckValue'];
        }

        if ($uncheckValue || ($uncheckValue === '0'))
        {
            $uncheck = form_hidden($name, $uncheckValue);
        }
        else
        {
            $uncheck = '';
        }

        return $uncheck . form_checkbox($name, (string) $value, $checked, $attributes);
    }

    public function checkboxGroup($data, $name, $value = 1, array $attributes = [], array $groupOptions = []): string
    {
        if (empty($groupOptions['labelOptions']['for']))
        {
            if (empty($attributes['id']))
            {
                $attributes['id'] = $this->getFieldId($data, $name);
            }

            $groupOptions['labelOptions']['for'] = $attributes['id'];
        }

        $content = $this->checkbox($data, $name, $value, $attributes);

        return $this->renderGroup($data, $name, $content, $groupOptions);
    }

    public function radio($data, $name, string $value, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        if ($this->getFieldValue($data, $name, $attributes) == $value)
        {
            $checked = true;
        }
        else
        {
            $checked = false;
        }

        return form_radio($name, $value, $checked, $attributes);
    }

    public function radioGroup($data, $name, $value, array $attributes = [], array $groupOptions = []): string
    {
        $content = $this->radio($data, $name, $value, $attributes);

        return $this->renderGroup($data, $name, $content, $groupOptions);
    }

    public function submit($data, $name, $value, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        return form_submit($name, $value, $attributes);
    }

    public function reset($data, $name, $value, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        return form_reset($name, $value, $attributes);
    }

    public function button($data, $name, $value, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        return form_button($name, $value, $attributes);
    }

    public function label(string $label = '', array $attributes = []): string
    {
        if (array_key_exists('id', $attributes))
        {
            $id = $attributes['id'];
        }
        else
        {
            $id = '';
        }

        return form_label($label, $id, $attributes);
    }

    public function datalist($data, $name, array $attributes = []): string
    {
        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);
   
        return form_datalist($name, $value, $attributes);
    }

    public function datalistGroup($data, $name, array $attributes = [], array $groupOptions = []) : string
    {
        $content = $this->datalist($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupOptions);
    }

    public function openFieldset($label = '', array $attributes = []): string
    {
        return form_fieldset($label, $attributes);
    }

    public function closeFieldset(): string
    {
        return form_fieldset_close();
    }    

    protected function _getValue($data, $name, $default = null)
    {
        if (is_object($data))
        {
            if (method_exists($data, 'toArray'))
            {
                $data = $data->toArray();
            }
            else
            {

                $data = (array) $data;
            }
        }

        if (array_key_exists($name, $data))
        {
            return $data[$name];
        }

        return $default;
    }

}