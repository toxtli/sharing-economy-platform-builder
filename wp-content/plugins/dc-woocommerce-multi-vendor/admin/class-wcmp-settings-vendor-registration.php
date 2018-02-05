<?php

class WCMp_Settings_Vendor_Registration {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmp_{$this->tab}_{$this->subsection}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;
        ?>
<h4>Setting panel to add extra fields in vendor registration page, along with the <a href="<?php echo admin_url('admin.php').'?page=wc-settings&tab=account'; ?>">WooCommerce registration form</a></h4>
        <div id="nav-menus-frame" ng-app="vendor_registration">
            <div id="menu-settings-column" class="metabox-holder" ng-controller="postbox_menu">
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox" ng-class="postboxClass">
                        <button ng-click="togglePostbox()" aria-expanded="false" class="handlediv button-link" type="button"><span class="screen-reader-text">Toggle panel: Format</span><span aria-hidden="true" class="toggle-indicator"></span></button>
                        <h3 class="hndl ui-sortable-handle">
                            <span>Form Fields</span>
                        </h3>
                        <div class="inside">
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('textbox', 'Text Box', $event)" class="button-secondary"><?php echo __('Textbox','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('email', 'Email', $event)" class="button-secondary"><?php echo __('Email','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('url', 'Url', $event)" class="button-secondary"><?php echo __('Url','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('textarea', 'Text Area', $event)" class="button-secondary"><?php echo __('Textarea','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('selectbox', 'Select Box', $event)" class="button-secondary"><?php echo __('List','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('checkbox', 'Checkbox', $event)" class="button-secondary"><?php echo __('Check Box','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('recaptcha', 'Recaptcha', $event)" class="button-secondary"><?php echo __('Recaptcha','dc-woocommerce-multi-vendor'); ?></a>
                            </p>    
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('file', 'Attachment', $event)" class="button-secondary"><?php echo __('Attachment','dc-woocommerce-multi-vendor'); ?></a>
                            </p> 
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('separator', 'Section', $event)" class="button-secondary"><?php echo __('Section','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="poststuff" ng-controller="postbox_content">
                <div id="post-body">
                    <div id="post-body-content">
                        <div id="wcmp-vendor-form">
                            <input type="button" value="Save" ng-click="saveFormData()" class="button-primary menu-save">
                            <a disabled="" ng-show="showSaveSpinner" class="button-secondary" href="#"><span style="visibility: visible; float: left;" class="spinner"></span></a>
                            
                            <div ng-if="fields.length === 0" class="wcmp-form-empty-container">Build your form here</div>
                            
                            <ul class="meta-box-sortables" ui-sortable="fieldSortableOptions" ng-model="fields">
                                <li ng-repeat="(parentIndex,field) in fields track by $index">
                                    <div class="postbox" ng-class="{'closed' : field.hidden }">
                                        <button aria-expanded="false" ng-click="togglePostboxField($index)" class="handlediv button-link" type="button"><span class="screen-reader-text">Toggle panel: Format</span><span aria-hidden="true" class="toggle-indicator"></span></button>
                                        <h2 class="hndle ui-sortable-handle" ng-dblclick="togglePostboxField($index)"><span>{{field.label}}</span></h2>
                                        <div class="inside">
                                            <div id="post-formats-select">
                                                <div ng-include src="partialUrl+field.partial"></div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <input type="button" value="Save" ng-click="saveFormData()" class="button-primary menu-save">
                            <a disabled="" ng-show="showSaveSpinner" class="button-secondary" href="#"><span style="visibility: visible; float: left;" class="spinner"></span></a>
                            <h4>Use [wcmp-regi-12], [wcmp-regi-6], [wcmp-regi-4] CSS class to customize the form</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
