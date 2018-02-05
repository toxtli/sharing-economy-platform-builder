<?php if(!defined('ABSPATH')) die('!'); ?><div class="w3eden">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 row-bottom-buffer">
                <input type="text" name="agent_email[subject]" class='form-control' placeholder="Enter subject"
                       value="<?php echo $agent_email['subject'] ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row-bottom-buffer">
                <?php
                $args = array(
                    'textarea_name' => 'agent_email[message]'
                );
                ?>
                <?php wp_editor($agent_email['message'], 'agent_email_message', $args );  ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row-bottom-buffer">
                <input type="email" name="agent_email[from_email]" class='form-control' placeholder="From address"
                       value="<?php echo $agent_email['from_email'] ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 row-bottom-buffer">
                <input type="text" name="agent_email[from_name]" class='form-control' placeholder="From name"
                       value="<?php echo $agent_email['from_name'] ?>"/>
            </div>
        </div>
    </div>
</div>