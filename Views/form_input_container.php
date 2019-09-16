<?php

if (empty($labelOptions['class']))
{
    $labelOptions['class'] = 'control-label';
}

$attrs = stringify_attributes($labelOptions);

?>

<div class="form-group">

    <label<?= $attrs;?>><?= $label;?></label>

    <?= $input;?>
    
    <!--
    <p class="help-block help-block-error">Name cannot be blank.</p>
-->

</div>