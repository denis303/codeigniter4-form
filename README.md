# CodeIgniter 4 Active Form

## Usage

```
$form = new Form($model, $errors);

echo $form->open();
echo $form->inputGroup($data, 'name', ['id' => 'myinput'], ['label' => 'Custom Label']);
echo $form->renderErrors();
echo $form->beginButtons();
echo $form->submitButton('Submit');
echo $form->endButtons();
echo $form->close();

```

Generated HTML Code:

```
<form method="post" accept-charset="utf-8">
    <div class="form-group">
        <label class="form-label">Category</label>
        <input type="text" name="translation_category" value="" class="form-control is-invalid">
    </div>
    <div class="alert alert-danger">The My Field field is required.</div>
    <div>
        <input type="submit" name="submit" value="Create" class="btn btn-primary">
    </div>
</form>
```

### Labels

You can specify the field names in the model.

```
class MyModel extends \CodeIgniter\Model
{
    
    protected $validationRules = [
        'my_input' => [
            'rules' => 'required',
            'label' => 'My Field'
        ]
    ];

}

```


You can use all types of input from the form helper of the framework.

Function names and parameters are similar to those used in the framework.

```
$form->passwordGroup($data, 'name', $attributes, $groupAttributes);
$form->checkboxGroup($data, 'name', $attributes, $groupAttributes);
$form->textareaGroup($data, 'name', $attributes, $groupAttributes);

and all other...
```

You can also use simple functions to generate a single input.

```
$form->input($data, 'name', $attributes);
$form->password($data, 'name', $attributes);
$form->checkbox($data, 'name', $attributes);
$form->textarea($data, 'name', $attributes);

and all other...
```