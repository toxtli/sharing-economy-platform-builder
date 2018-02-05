<?php
if(!defined('ABSPATH')) die('!');

/**
 * @variable    $formsetting
 * @uses        Contains form element configurations
 * @origin      Controller: contactforms, Method: showform()
 */
$formsetting_raw = $formsetting;
$forms_view = unserialize($formsetting_raw['fields']);
$forms_pref = unserialize($formsetting_raw['fieldsinfo']);
?>
    <script src="<?php echo base_url();?>/templates/default/js/jquery.form.min.js"></script>

    <script>
        $(function(){
            // $("select").selectpicker({style: 'btn btn-primary', menuStyle: 'dropdown-inverse'});
            $('.ttipf').tooltip({placement:'left'});
        });
    </script>

<div id="formarea">
<form class="form" id="form" role="form" action="?section=contact-forms&view=submit" method="POST" enctype="multipart/form-data" >
    <input type="hidden" id="formid" name="form_id" value="<?php echo $form_id ?>" />
    <?php
    foreach ( $forms_view as $id => $type ) {
        $cur_pref = $forms_pref[$id];
        $cur_pref['id'] = $id;
    ?>
    <div class="form-group">
        <label for="field_" style="display: block;clear: both"><?php echo esc_attr($cur_pref['label']) ?></label>
        <?php
            if ( isset($commonfields[$forms_view[$id]]) )
                echo $formfields->$commonfields[$forms_view[$id]]['type']( $cur_pref );
            else if ( isset($advanced_fields[$forms_view[$id]]) )
                echo $advancedfields->$advanced_fields[$forms_view[$id]]['type']( $cur_pref );
            else
                echo $formfields->$forms_view[$id]( $cur_pref );
        ?>
    </div>
    <?php
    }
    ?>
    <button type="submit" id="submit" class="btn btn-info btn-block"><?php echo ((isset($formsetting_raw['buttontext'])==false||$formsetting_raw['buttontext']=='')?"Submit":esc_attr($formsetting_raw['buttontext'])) ?></button>
</form>
</div>

</div>

<Script>
    $(document).ready(function() {

        $('input,select,textarea').each(function() {
            var el = this;
            el.oninvalid = function(e) {
                e.target.setCustomValidity("");
                if (!e.target.validity.valid) {
                    e.target.setCustomValidity($(el).attr('vmsg'));
                }
            };
            el.oninput = function(e) {
                e.target.setCustomValidity("");
            };
        });

        // ajax submit
        var options = {
            target:        '#formarea',   // target element(s) to be updated with server response
            beforeSubmit:  function(){
                $('#submit').remove();
                $('#form').append("<button type='button' class='btn btn-link btn-block'><i class='fa fa-spinner fa-spin'></i></button>");
            },  // pre-submit callback
            success:       function(){

            }
        };
        $('#form').submit(function(){
            $(this).ajaxSubmit(options);
            return false;
        });
    })
</Script>

<?php die(); ?>