<div class="margin center">
    <div class="margin center wide">
        <div class="fontLarge"><?php echo $this->getVar('title'); ?></div>
    </div>
    <?php
        $notice = $this->getVar('installationRequestNotice');
        if (!empty($notice['type']) && !empty($notice['text'])) {
            echo '<div class="margin center wide">';
                echo '<div class="'.$notice['type'].'">'.$notice['text'].'</div>';
            echo '</div>';
        }
    ?>
    <div class="margin wide">
        <form action="/" method="post">
            <div class="form left">
                <?php
                    foreach ($this->getVar('textFields') as $fieldName => $field) {
                        echo '<div class="field">';
                            $placeholder = empty($field['placeholder']) ? '' : 'placeholder="'.$field['placeholder'].'"';
                            $required = empty($field['required']) ? '' : 'required';
                            echo '<input type="text" '.$placeholder.' name="'.$fieldName.'" class="fontNormal" '.$required.' />';
                        echo '</div>';
                    }
                ?>
                <div class="field">
                    <select name="software" id="software" class="fontNormal" onchange="changeTimeSlots(this)">
                        <option value="">-- Choose software --</option>
                        <?php
                            foreach ($this->getVar('software') as $softwareId => $software) {
                                $softwareName = $software['softwareName'];
                                $disabled = empty($software['timeSlots']) ? 'disabled="disabled"' : '';
                                echo '<option value="' . $softwareId . '" ' . $disabled . '>' . $softwareName . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <?php
                    foreach ($this->getVar('software') as $softwareId => $software) {
                        echo '<div div class="timeSlots" id="software' . $softwareId . 'TimeSlots" style="display: none;">';
                            foreach ($software['timeSlots'] as $strDate => $dateSlots) {
                                echo '<div>' . $strDate . '</div>';
                                echo '<div class="dateSlot">';
                                    foreach ($dateSlots as $strTime => $timeSlot) {
                                        $id = "software{$softwareId}TimeSlot{$timeSlot['slot']}";
                                        $disabled = empty($timeSlot['invalid']) ? '' : 'disabled="disabled"';
                                        echo '<input type="radio" id="' . $id . '" value="' . $timeSlot['slot'] . '" '.$disabled.' name="timeSlot" class="timeSlotRadio" required />';
                                        echo '<label for="' . $id . '" '.$disabled.'>' . $strTime . '</label>';
                                    }
                                echo '</div>';
                            }
                        echo '</div>';
                    }
                ?>
                <div class="">
                    <input type="submit" value="Submit Request" class="fontMedium fontColor" />
                </div>
            </div>
        </form>
    </div>
    <div class="margin wide">
        <div class="left">
            <div><a href="/employee"><?php echo $this->getVar('employeeLink'); ?></a></div>
        </div>
    </div>
</div>