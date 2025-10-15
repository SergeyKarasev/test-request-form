<div class="margin center">
    <div class="margin center wide">
        <div class="fontLarge"><?php echo $this->getVar('title'); ?></div>
    </div>
    <div class="margin center wide">
        <div><?php echo $this->getVar('note'); ?></div>
        <?php
            $notice = $this->getVar('authNotice');
            if (!empty($notice['type']) && !empty($notice['text'])) {
                echo '<div class="'.$notice['type'].'">'.$notice['text'].'</div>';
            }
        ?>
    </div>
    <div class="margin wide">
        <form action="/auth" method="post">
            <div class="form left">
                <?php
                    foreach ($this->getVar('textFields') as $fieldName => $field) {
                        echo '<div class="field">';
                            $placeholder = empty($field['placeholder']) ? '' : 'placeholder="'.$field['placeholder'].'"';
                            $required = empty($field['required']) ? '' : 'required';
                            $type = empty($field['password']) ? 'type="text"' : 'type="password"';
                            echo '<input '.$type.' '.$placeholder.' name="'.$fieldName.'" class="fontNormal" '.$required.' />';
                        echo '</div>';
                    }
                ?>
                <div class="">
                    <input type="submit" value="Auth" class="fontMedium fontColor" />
                </div>
            </div>
        </form>
    </div>
    <div class="margin wide">
        <div class="left">
            <div><a href="/"><?php echo $this->getVar('formLink'); ?></a></div>
        </div>
    </div>
</div>