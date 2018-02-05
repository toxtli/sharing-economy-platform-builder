<?php

/**
 * The Template for displaying vendor registration form.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor_registration_form.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.4.3
 */
global $WCMp;
if (!empty($wcmp_vendor_registration_form_data) && is_array($wcmp_vendor_registration_form_data)) {
    $sep_count = 0;
    foreach ($wcmp_vendor_registration_form_data as $key => $value) {
        switch ($value['type']) {
            case 'separator':
                ?>
                <div class="clearboth"></div>
                </div>
                <div class="wcmp_regi_form_box">
                <h3 class="reg_header2"><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?></h3>
                <?php
                break;
            case 'textbox':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="text" value="<?php if (!empty($_POST['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($_POST['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="textbox" />
                </div>
                <?php
                break;
            case 'email':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="email" value="<?php if (!empty($_POST['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($_POST['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="email" />
                </div>
                <?php
                break;
            case 'textarea':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <textarea <?php if(!empty($value['limit'])){ echo 'maxlength="'.$value['limit'].'"'; } ?> name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['defaultValue']; ?>"><?php if (!empty($_POST['wcmp_vendor_fields'][$key]["value"])){ echo esc_attr($_POST['wcmp_vendor_fields'][$key]["value"]); } ?></textarea>
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="textarea" />
                </div>
                <?php
                break;
            case 'url': 
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="url" value="<?php if (!empty($_POST['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($_POST['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="url" />
                </div>
                <?php
                break;
            case 'selectbox':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="selectbox" />
                    <?php
                     switch ($value['selecttype']){
                         case 'dropdown':
                            ?>
                            <select class="select_box" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" <?php if($value['required']){ echo 'required="required"'; }?>>
                            <?php
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <option value="<?php echo $option_value['value']; ?>" <?php if($option_value['selected']){ echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                            </select>
                             <?php
                             break;
                         case 'radio':
                             if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <p> <input type="radio" <?php if($option_value['selected']){ echo 'checked="checked"'; } ?> name="wcmp_vendor_fields[<?php echo $key; ?>][value]" value="<?php echo $option_value['value']; ?>"> <?php echo $option_value['label']; ?></p>
                                    <?php
                                }
                            }
                             break;
                         case 'checkboxes':
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <p> <input type="checkbox" <?php if($option_value['selected']){ echo 'checked="checked"'; } ?> name="wcmp_vendor_fields[<?php echo $key; ?>][value][]" value="<?php echo $option_value['value']; ?>"> <?php echo $option_value['label']; ?></p>
                                    <?php
                                }
                            }
                             break;
                         case 'multi-select':
                             ?>
                            <select class="select_box" style="min-height: 59px;" name="wcmp_vendor_fields[<?php echo $key; ?>][value][]" <?php if($value['required']){ echo 'required="required"'; }?> multiple="">
                            <?php
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <option value="<?php echo $option_value['value']; ?>" <?php if($option_value['selected']){ echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                            </select>
                            <?php
                            break;
                     }
                    ?>
                </div>
                <?php
                break;

            case 'checkbox':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <input type="checkbox" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" <?php if($value['defaultValue'] == 'checked'){ echo 'checked="checked"';} ?>  <?php if($value['required']){ echo 'required="required"'; }?> />
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="checkbox" />
                </div>
                <?php
                break;
            case 'recaptcha':
                wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js');
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <?php echo $value['script']; ?>
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" value="Verified" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="checkbox" />
                </div>
                <?php
                break;
            case 'file':
                ?>
                <div class="<?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="file" name="wcmp_vendor_fields[<?php echo $key; ?>][]" <?php if($value['required']){ echo 'required="required"'; }?> <?php if($value['muliple']){ echo 'multiple="true"'; }?> />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="file" />
                </div>
                <?php
                break;
        }
    }
    //echo '<input type="hidden" value="'.  json_encode($wcmp_vendor_registration_form_data).'" />';
}