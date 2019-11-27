<?php
/**
 * @author denis303 <mail@denis303.com>
 * @license MIT
 * @link http://denis303.com
 */
namespace denis303\codeigniter4;

use CodeIgniter\Entity;
use PhpTheme\Html\HtmlHelper;
use ReflectionObject;

class Form
{

    protected $_model;

    protected $_reflection;

    protected $_errors = [];

    public $errorClass = 'is-invalid';

    public $errorTemplate = '<div{attributes}>{error}</div>';

    public $messageTemplate = '<div{attributes}>{message}</div>';

    public $labelTemplate = '<label{attributes}>{label}</label>';

    public $groupTemplate = '<div class="form-group"{attributes}>{label}{input}</div>';

    public $errorAttributes = ['class' => 'alert alert-danger'];

    public $messageAttributes = ['class' => 'alert alert-info'];

    public $labelAttributes = ['class' => 'form-label'];

    public $inputAttributes = ['class' => 'form-control'];

    public $passwordAttributes = ['class' => 'form-control'];

    public $uploadAttributes = [];

    public $textareaAttributes = ['class' => 'form-control'];

    public $multiselectAttributes = [];

    public $dropdownAttributes = ['class' => 'form-control'];

    public $checkboxAttributes = [];

    public $radioAttributes = [];

    public $submitAttributes = ['class' => 'btn btn-primary'];

    public $resetAttributes = [];

    public $buttonAttributes = [];

    public $datalistAttributes = [];

    public $fieldsetAttributes = [];

    public $formAttributes = [];

    public $groupAttributes = [];

    public $groupOptions = [];

    public $buttonsTag = 'div';

    public $buttonsAttributes = [];

    public function __construct(object $model, array $errors = [])
    {
        $this->_model = $model;

        $this->_errors = $errors;

        helper(['form']);
    }

    protected function _getReflection()
    {
        if (!$this->_reflection)
        {
            $this->_reflection = new ReflectionObject($this->_model);
        }

        return $this->_reflection;
    }

    protected function _getFieldLabel($data, $name)
    {
        $reflection = $this->_getReflection();

        $properties = $reflection->getDefaultProperties();

        if (!empty($properties['validationRules'][$name]['label']))
        {
            return $properties['validationRules'][$name]['label'];
        }

        return $name;
    }

    public function fieldHasError($data, $name, array $attributes = [])
    {
        if ($this->getFieldError($data, $name, $attributes))
        {
            return true;
        }

        return false;
    }    

    public function addErrorClass($data, $name, array $attributes = [])
    {
        if ($this->errorClass && $this->fieldHasError($data, $name, $attributes))
        {
            if (!array_key_exists('class', $attributes))
            {
                $attributes['class'] = $this->errorClass;
            }
            else
            {
                $attributes['class'] .= ' ' . $this->errorClass;
            }
        }

        return $attributes;
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

        return $this->_getFieldLabel($data, $name);
    }

    public function getFieldValue($data, $name, array $attributes = [], $default = '')
    {
        if (array_key_exists('value', $attributes))
        {
            return (string) $attributes['value'];
        }

        if (is_object($data))
        {
            if ($data instanceof Entity)
            {
                $return = $data->$name;

                if ($return !== null)
                {
                    return (string) $data->$name;
                }
            }

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
            return (string) $data[$name];
        }

        return (string) $default;
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

    public function renderError($error, array $attributes = [])
    {
        $attributes = HtmlHelper::mergeAttributes($this->errorAttributes, $attributes);

        if (!$error)
        {
            return '';
        }

        return strtr(
            $this->errorTemplate, 
            [
                '{error}' => $error,
                '{attributes}' => stringify_attributes($attributes)
            ]
        );
    }

    public function renderMessage($message, array $attributes = [])
    {
        $attributes = HtmlHelper::mergeAttributes($this->messageAttributes, $attributes);

        if (!$message)
        {
            return '';
        }

        return strtr(
            $this->messageTemplate, 
            [
                '{message}' => $message,
                '{attributes}' => stringify_attributes($attributes)
            ]
        );
    }

    public function renderErrors($errors = [], $renderAllErrors = true, array $attributes = [])
    {
        $return = '';

        if ($renderAllErrors)
        {
            $errors = array_merge($this->_errors, $errors);
        }

        foreach($errors as $error)
        {
            $return .= $this->renderError($error,  $attributes);
        }

        return $return;
    }

    public function renderMessages($messages = [], $attributes = [])
    {
        $return = '';

        foreach($messages as $message)
        {
            $return .= $this->renderMessage($message,  $attributes);
        }

        return $return;
    }

    public function renderLabel($label, array $attributes = [])
    {
        $attributes = HtmlHelper::mergeAttributes($this->labelAttributes, $attributes);

        return strtr(
            $this->labelTemplate, 
            [
                '{label}' => $label,
                '{attributes}' => stringify_attributes($attributes)
            ]
        );
    }

    public function renderGroup($data, $name, $content, array $options = [])
    {
        $attributes = array_merge($this->groupOptions, $options);

        if (array_key_exists('prefix', $options))
        {
            $content = $options['prefix'] . $content;
        }

        if (array_key_exists('suffix', $options))
        {
            $content = $content . $options['suffix'];
        }

        if (array_key_exists('template', $options))
        {
            $template = $options['template'];
        }
        else
        {
            $template = $this->groupTemplate;
        }

        $labelAttributes = [];

        if (array_key_exists('labelAttributes', $options))
        {
            $labelAttributes = $options['labelAttributes'];
        }

        $errorAttributes = [];

        if (array_key_exists('errorAttributes', $options))
        {
            $errorAttributes = $options['errorAttributes'];
        }

        if (array_key_exists('attributes', $options))
        {
            $attributes = $options['attributes'];
        }
        else
        {
            $attributes = [];
        }

        $attributes = HtmlHelper::mergeAttributes($this->groupAttributes, $attributes);

        $options['{input}'] = $content;

        $options['{label}'] = $this->renderLabel($this->getFieldLabel($data, $name, $options), $labelAttributes);

        $options['{error}'] = $this->renderError($this->getFieldError($data, $name, $options), $errorAttributes);

        $options['{attributes}'] = stringify_attributes($attributes);

        return strtr($template, $options);
    }

    public function open($action = null, $attributes = [], array $hidden = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->formAttributes, $attributes);

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

        $attributes = HtmlHelper::mergeAttributes($this->inputAttributes, $attributes);

        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        $attributes = $this->addErrorClass($data, $name, $attributes);

        return form_input($name, $value, $attributes, $type);
    }

    public function inputGroup($data, $name, array $attributes = [], array $groupAttributes = [])
    {
        $content = $this->input($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupAttributes);
    }

    public function password($data, $name, array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->passwordAttributes, $attributes);

        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        $attributes = $this->addErrorClass($data, $name, $attributes);

        return form_password($name, $value, $attributes);
    }

    public function passwordGroup($data, $name, array $attributes = [], array $groupAttributes = [])
    {
        $content = $this->password($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupAttributes);
    }

    public function upload($data, $name, array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->uploadAttributes, $attributes);

        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        $attributes = $this->addErrorClass($data, $name, $attributes);

        return form_upload($name, $value, $attributes);
    }

    public function uploadGroup($data, $name, array $attributes = [], array $groupAttributes = [])
    {
        $content = $this->upload($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupAttributes);
    }

    public function textarea($data, $name, array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->textareaAttributes, $attributes);

        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        $attributes = $this->addErrorClass($data, $name, $attributes);

        return form_textarea($name, $value, $attributes);
    }

    public function textareaGroup($data, $name, array $attributes = [], array $groupAttributes = [])
    {
        $content = $this->textarea($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupAttributes);
    }

    public function multiselect($data, $name, array $list = [], array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->multiselectAttributes, $attributes);

        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        $attributes = $this->addErrorClass($data, $name, $attributes);

        return form_multiselect($name, $list, $value, $attributes);
    }

    public function multiselectGroup($data, $name, $list = [], array $attributes = [], array $groupAttributes = []): string
    {
        $content = $this->multiselect($data, $name, $list, $attributes);

        return $this->renderGroup($data, $name, $content, $groupAttributes);
    }

    public function dropdown($data, $name, $list = [], array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->dropdownAttributes, $attributes);

        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);

        $attributes = $this->addErrorClass($data, $name, $attributes);

        return form_dropdown($name, $list, $value, $attributes);
    }

    public function dropdownGroup($data, $name, $list = [], array $attributes = [], array $groupAttributes = []): string
    {
        $content = $this->dropdown($data, $name, $list, $attributes);

        return $this->renderGroup($data, $name, $content, $groupAttributes);
    }

    public function checkbox($data, $name, $value = 1, array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->checkboxAttributes, $attributes);

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

        $attributes = $this->addErrorClass($data, $name, $attributes);

        return $uncheck . form_checkbox($name, (string) $value, $checked, $attributes);
    }

    public function checkboxGroup($data, $name, $value = 1, array $attributes = [], array $groupAttributes = []): string
    {
        if (empty($groupAttributes['labelAttributes']['for']))
        {
            $attributes['id'] = $this->getFieldId($data, $name, $attributes);

            $groupAttributes['labelAttributes']['for'] = $attributes['id'];
        }

        $content = $this->checkbox($data, $name, $value, $attributes);

        return $this->renderGroup($data, $name, $content, $groupAttributes);
    }

    public function radio($data, $name, string $value, array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->radioAttributes, $attributes);

        $name = $this->getFieldName($data, $name, $attributes);

        if ($this->getFieldValue($data, $name, $attributes) == $value)
        {
            $checked = true;
        }
        else
        {
            $checked = false;
        }

        $attributes = $this->addErrorClass($data, $name, $attributes);

        return form_radio($name, $value, $checked, $attributes);
    }

    public function radioGroup($data, $name, $value, array $attributes = [], array $groupAttributes = []): string
    {
        $content = $this->radio($data, $name, $value, $attributes);

        return $this->renderGroup($data, $name, $content, $groupAttributes);
    }

    public function submit($name, $value, array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->submitAttributes, $attributes);

        return form_submit($name, $value, $attributes);
    }

    public function reset($name, $value, array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->resetAttributes, $attributes);

        return form_reset($name, $value, $attributes);
    }

    public function button($name, $value, array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->buttonAttributes, $attributes);

        return form_button($name, $value, $attributes);
    }

    public function submitButton($label, array $attributes = [])
    {
        return $this->submit('submit', $label, $attributes);
    }

    public function resetButton($label, array $attributes = [])
    {
        return $this->reset('reset', $label, $attributes);
    }    

    public function label(string $label = '', array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->labelAttributes, $attributes);

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
        $attributes = HtmlHelper::mergeAttributes($this->datalistAttributes, $attributes);

        $name = $this->getFieldName($data, $name, $attributes);

        $value = $this->getFieldValue($data, $name, $attributes);
   
        return form_datalist($name, $value, $attributes);
    }

    public function datalistGroup($data, $name, array $attributes = [], array $groupAttributes = []) : string
    {
        $content = $this->datalist($data, $name, $attributes);

        return $this->renderGroup($data, $name, $content, $groupAttributes);
    }

    public function openFieldset($label = '', array $attributes = []): string
    {
        $attributes = HtmlHelper::mergeAttributes($this->fieldsetAttributes, $attributes);

        return form_fieldset($label, $attributes);
    }

    public function closeFieldset(): string
    {
        return form_fieldset_close();
    }

    public function beginButtons()
    {
        return HtmlHelper::beginTag($this->buttonsTag, $this->buttonsAttributes);
    }

    public function endButtons()
    {
        return HtmlHelper::endTag($this->buttonsTag);
    }

}