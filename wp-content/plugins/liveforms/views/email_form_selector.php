<?php if(!defined('ABSPATH')) die('!'); ?><div class="w3eden">
    <div class="container-fluid">
        <div class="row row-bottom-buffer">
            <div class="col-md-12">
                <select class="form-control" name="selected_form">
                    <option class="form-id" data-shortcodes="" value="">Select a form</option>
                    <?php foreach ($forms as $key => $data) { ?>
                        <?php
                        $form_title = $data['post_title'];
                        $form_ID = $data['ID'];
                        ?>
                        <option
                            id="selection_<?php echo $form_ID ?>" <?php if ($selected_form == $form_ID) echo 'selected="selected"' ?>
                            class="form-id"
                            value="<?php echo $form_ID ?>"
                            data-shortcodes="<?php echo $shortcodes[$key] ?>"><?php echo $form_title ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div id="shortcodes_list" class="col-md-12 ">
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    jQuery(function ($) {
        $(document).ready(function () {
            $('.form-id').on('click', function () {
                str_shortcodes = $(this).attr('data-shortcodes');
                populate_shortcode_list(str_shortcodes);
            });

            // For initialization
            str_shortcodes = $('#selection_<?php echo $selected_form ?>').attr('data-shortcodes');
            populate_shortcode_list(str_shortcodes);
        })

        function populate_shortcode_list(str_shortcodes) {
			var token_html = '<div class="row"><div class="col-md-12">'
                        + '<div class="shortcode-group">'
                        + '<div class="shortcode-title">' + 'Token' + '</div>'
                        + '<input type="text" readonly="readonly" class="form-control shortcode-code " value=" {' + 'token' + '} ">'
                        + '</div></div></div>';
            if (str_shortcodes != '' && str_shortcodes != undefined) {
                shortcodes = str_shortcodes.split(',');
                shortcode_list_html = '<table class="table table-striped"><thead><th>Shortcodes</th></thead><tbody>';
                for (i=0; i < shortcodes.length; i++) {
                    array_short = shortcodes[i].split(':');
                    row_html = '<div class="row"><div class="col-md-12">'
                        + '<div class="shortcode-group">'
                        + '<div class="shortcode-title">' + array_short[1] + '</div>'
                        + '<input type="text" readonly="readonly" class="form-control shortcode-code " value=" {' + array_short[0] + '} ">'
                        + '</div></div></div>';
                    shortcode_list_html += ("<tr><td>" + row_html + "</td></tr>");
                }
                shortcode_list_html += "</tbody></table>";
                jQuery('#shortcodes_list').html(token_html + shortcode_list_html);
            } else {
                jQuery('#shortcodes_list').html(token_html + "No shortcodes available");
            }
        }
    })
</script>