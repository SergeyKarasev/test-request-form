<div class="margin center">
    <div class="margin center wide">
        <div class="fontLarge"><?php echo $this->getVar('title'); ?></div>
    </div>
    <?php
        $employee = $this->getVar('employee');
            echo '<div class="margin center wide">';
                echo '<div class="success">'.$employee['employee'].' | '.$employee['email'].' | 
                        <a href="#" onclick="clickLogout()">logout</a></div>';
            echo '</div>';
    ?>
    <form method="post" action="/employee" style="display: none;">
        <input type="submit" value="Logout" id="employeeLogout"/>
    </form>
    <div class="margin center wide">
        <div class="form">
        <?php
            $installationRequests = $this->getVar('installationRequests');
            foreach ($installationRequests as $installation) {
                echo '<div class="row margin">';
                    echo '<div>Installation: '.$installation['date'].' | Software: '.$installation['software'].'</div>';
                    echo '<div>'.$installation['note'].'</div>';
                echo '</div>';
            }
        ?>
        </div>
    </div>
    <div class="margin wide">
        <div class="left">
            <div><a href="/"><?php echo $this->getVar('formLink'); ?></a></div>
        </div>
    </div>
</div>