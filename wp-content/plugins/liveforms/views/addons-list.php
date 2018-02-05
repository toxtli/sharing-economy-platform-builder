<?php if(!defined('ABSPATH')) die('!'); ?>
<script type="text/javascript" src="<?php echo plugins_url();?>/liveforms/js/jquery.form.js"></script>
<link rel="stylesheet" href="<?php echo plugins_url('/liveforms/css/chosen.css'); ?>" />
<script language="JavaScript" src="<?php echo plugins_url('/liveforms/js/chosen.jquery.min.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/liveforms/bootstrap/css/bootstrap.css');?>" />

<style>

    .lfplinks .fa{
        margin-left: 10px;
        -webkit-transition: ease-in-out 300ms;
        -moz-transition: ease-in-out 300ms;
        -ms-transition: ease-in-out 300ms;
        -o-transition: ease-in-out 300ms;
        transition: ease-in-out 300ms;
    }
    .lfplinks a:hover .fa{
        -webkit-transition: ease-in-out 300ms;
        -moz-transition: ease-in-out 300ms;
        -ms-transition: ease-in-out 300ms;
        -o-transition: ease-in-out 300ms;
        transition: ease-in-out 300ms;
        transform: scale(1.3);
    }
    .lfplinks *{
        color: #00a379 !important;
        font-weight: 500;
    }

    .lfplinks a:hover{
        text-decoration: none;
    }

    input{
        padding: 7px;
    }
    #wphead{
        border-bottom:0px;
    }
    #screen-meta-links{
        display: none;
    }
    .wrap{
        margin: 0px;
        padding: 0px;
    }
    #wpbody{
        margin-left: -19px;
    }
    select{
        min-width: 150px;
    }



    .w3eden .btn{
        border-radius: 0.2em !important;
    }
    .well{ box-shadow: none !important; background: #FFFFFF !important; } .btn{ border: 0 !important; }

    .w3eden .nav-pills a{
        background: #f5f5f5;
    }

    #addonmodal{ background: rgba(0,0,0,0.7); z-index: 9999; }

    #addonmodal .modal-dialog{
        margin-top: 100px;

    }

    .w3eden .form-control,
    .w3eden .nav-pills a{
        border-radius: 0.2em !important;
        box-shadow: none !important;
        font-size: 9pt !important;
    }

    .wpdm-spin{
        -webkit-animation: spin 2s infinite linear;
        -moz-animation: spin 2s infinite linear;
        -ms-animation: spin 2s infinite linear;
        -o-animation: spin 2s infinite linear;
        animation: spin 2s infinite linear;
    }

    @keyframes "spin" {
        from {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -webkit-transform: rotate(359deg);
            -moz-transform: rotate(359deg);
            -o-transform: rotate(359deg);
            -ms-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-moz-keyframes spin {
        from {
            -moz-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -moz-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-webkit-keyframes "spin" {
        from {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -webkit-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-ms-keyframes "spin" {
        from {
            -ms-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -ms-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    @-o-keyframes "spin" {
        from {
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        to {
            -o-transform: rotate(359deg);
            transform: rotate(359deg);
        }

    }

    .panel-heading h3.h{
        font-size: 11pt;
        font-weight: 700;
        margin: 0;
        padding: 5px 10px;
        font-family: 'Open Sans';
    }

    .panel-heading .btn.btn-primary{
        margin-top: -4px;
        margin-right: -6px;
        border-radius: 3px;
        border:1px solid rgba(255,255,255,0.8);
        -webkit-transition: all 400ms ease-in-out;
        -moz-transition: all 400ms ease-in-out;
        -o-transition: all 400ms ease-in-out;
        transition: all 400ms ease-in-out;
    }

    .panel-heading .btn.btn-primary:hover{
        margin-top: -4px;
        margin-right: -6px;
        border-radius: 3px;
        border:1px solid rgba(255,255,255,1);

    }

    .alert-info {
        background-color: #DFECF7 !important;
        border-color: #B0D1EC !important;
    }

    ul.nav li a:active,
    ul.nav li a:focus,
    ul.nav li a{
        outline: none !important;
    }

    .w3eden .nav-pills li.active a,
    .btn-primary,
    .w3eden .panel-primary > .panel-heading{
        background-image: linear-gradient(to bottom, #2081D5 0px, #1B6CB2 100%) !important;
    }
    .w3eden .panel-default > .panel-heading {
        background-image: linear-gradient(to bottom, #F5F5F5 0px, #E1E1E1 100%);
        background-repeat: repeat-x;
    }

    #modalcontents .wrap h2{ display: none; }

</style>

<div class="wrap w3eden">


<div class="container-fluid">

<div class="row" id="addonlist" style="margin-top: -15px">


    <?php
$data = json_decode($data);
$cats = json_decode($cats);


    ?>
    <div class="col-md-12"><div class="panel panel-default"><div class="panel-heading"><h3 style="font-size: 12pt;letter-spacing: 1px"><i class="fa fa-plug"></i> <?php echo __('LiveForm Add-Ons','liveforms'); ?></h3></div><div class="panel-body">
<div style="color: #2fb451;letter-spacing: 0.4px;text-align: center">
    Buy pro version to get access to all add-ons for free  <span class="lfplinks">&nbsp;<a target="_blank" href="https://liveforms.org/form-builder-pricing-plans-wordpress-plugin/"><i class="fa fa-bars"></i> View Pricing</a> &nbsp;<a target="_blank" href="https://liveforms.org/downloads/add-ons/"> <i class="fa fa-plug"></i>  View Add-ons</a></span>
</div></div></div>
        </div>


    <?php
    if(isset($data)){
foreach($data as $package){
    if((int)$package->price > 0 && count($package->categories) > 0){

 ?>
    <div class="col-md-4 col-sm-6 col-xs-12 all <?php echo implode(" ", $package->cats); ?>">
<div class="panel panel-default">
    <div class="panel-body" style="min-height: 130px;height: 130px;overflow: hidden">
<div class="media">
    <a href="#" class="pull-left">
        <?php if($package->thumbnail!=''){ ?>
        <img width="100px" class="img-rounded" src="<?php echo $package->thumbnail; ?>" />
        <?php } ?>
    </a>
    <div class="media-body">
        <b><a href="<?php echo $package->link; ?>"><?php echo $package->post_title; ?></a></b><br/>
        <p><?php echo $package->excerpt; ?></p>
    </div>
</div>
        </div>
    <div class="panel-footer" style="line-height: 30px;">
        <div class="pull-right" style="margin-top: -2px">
            <?php if($package->price>0){ ?>
            <a class="btn btn-sm btn-primary btn-purchase" href="<?php echo $package->link; ?>" target="_blank" rel="<?php echo $package->ID; ?>" style="border: 0;border-radius: 2px"><i class="fa fa-shopping-cart"></i> &nbsp;Buy Now &nbsp; <span class="label label-warning" style="font-size: 8pt;padding: 1px 5px;margin-top: 1px"><?php echo $package->currency.$package->price; ?></span> </a>
            <?php } else { ?>
                <a class="btn btn-sm btn-primary btn-install" href="<?php echo $package->link; ?>" target="_blank" style="border: 0;border-radius: 2px"><i class="fa fa-download"></i> &nbsp;Download & Install</a>
            <?php } ?>
        </div>
        <span class="label label-success" style="font-size: 11px;"><i class="fa fa-database"></i> &nbsp;<?php echo $package->pinfo->version; ?></span> &nbsp;<span class="label label-info" style="font-size: 11px;"><i class="fa fa-th"></i> &nbsp;<?php echo $package->pinfo->package_size; ?></span>
    </div>
</div>
        </div>
<?php
}}}
?>

</div>

    </div>
    <div class="modal fade" id="addonmodal" tabindex="-1" role="dialog" aria-labelledby="addonmodalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Add-On Installer</h4>
                </div>
                <div class="modal-body" id="modalcontents">
                    <i class="fa fa-spinner fa-spin"></i> Please Wait...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <a type="button" id="prcbtn" target="_blank" href="http://www.wpdownloadmanager.com/cart/" class="btn btn-success" style="display: none" onclick="jQuery('#addonmodal').modal('hide')">Checkout</a>
                </div>
            </div>
        </div>
    </div>
    </div>

<script>
    jQuery(function(){
        jQuery('.nav-pills a').click(function(){
                jQuery('#addonlist .all').fadeOut();
                jQuery('.'+this.rel).fadeIn();
                jQuery('#prcbtn').hide();
                jQuery('.nav-pills li').removeClass('active');
                jQuery(this).parent().addClass('active');
        });

        jQuery(".btn-install, .btn-purchase").click(function(){
            jQuery('#modalcontents').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
        });
        jQuery('#addonmodal').on('shown.bs.modal', function (e) {
            if(jQuery(e.relatedTarget).hasClass('btn-install')){
                jQuery('.modal-dialog').css('width','500px');
                jQuery('.modal-footer .btn-danger').html('Close');
                jQuery('#modalcontents').css('padding','20px').css('background','#ffffff');
                jQuery.post(ajaxurl,{action:'wpdm-install-addon', addon: e.relatedTarget.rel}, function(res){
                    jQuery('#modalcontents').html(res.replace('Return to Plugin Installer',''));
                })
            }

            if(jQuery(e.relatedTarget).hasClass('btn-purchase')){
                jQuery('.modal-dialog').css('width','800px');
                jQuery('.modal-footer').css('margin',0);
                jQuery('.modal-footer .btn-danger').html('<i class="fa fa-spinner fa-spin"></i> Please Wait...');
                jQuery('#modalcontents').css('padding',0).css('background','#f2f2f2').html("<iframe onload=\"jQuery('.modal-footer .btn-danger').html('Continue Shopping...');jQuery('#prcbtn').show();\" style='width: 100%;padding-top: 20px; background: #f2f2f2;height: 300px;border: 0' src='http://www.wpdownloadmanager.com/?addtocart="+e.relatedTarget.rel+"'></iframe>");
            }
        })


    });
</script>

