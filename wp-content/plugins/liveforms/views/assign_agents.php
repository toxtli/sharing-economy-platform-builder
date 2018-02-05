<?php if(!defined('ABSPATH')) die('!'); ?><div class="w3eden">
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form class="form" role="form" method="post"
                  action="?section=contact-forms&action=complete_agent_assignment">
                <input type='hidden' name='form_id' value='<?php echo $form_data['id'] ?>'/>

                <div class="panel panel-default">
                    <div class="panel-heading"><b>My Agents</b></div>
                    <div class="panel-body np">
                        <table class="table">
                            <thead>
                                <th>ID</th>
                                <th>Agent Name</th>
                                <th>Email</th>
                                <th><div class='pull-right'>Assign</div></th>
                            </thead>
                            <tbody>
                                <?php foreach ($agents_available as $agent) { ?>
                                <tr>
                                <td><?php echo $agent['id'] ?></td>
                                <td><?php echo $agent['agentname'] ?></td>
                                <td><?php echo $agent['email'] ?></td>
                                <td><div class="pull-right">
                                        <?php if (intval($form_data['agent']) == intval($agent['id'])) { ?>
                                            <a href="#" disabled="disabled" class='btn btn-embossed btn-xs ttip' title='Currently assigned'><i class='fa fa-flag'></i></a>
                                        <?php } else { ?>
                                            <a class='btn btn-primary btn-xs ttip' href="<?php echo base_url("?section=contact-forms&action=complete_agent_assignment&agent_id=").$agent['id']."&form_id=".$form_data['id'] ?>" title='Assign'><i class='fa fa-flag'></i></a>
                                        <!--<input type="radio" name="assign" value="<?php /*echo $agent['id'] */?>" <?php /*if (intval($form_data['agent']) == intval($agent['id'])) echo 'checked="checked"' */?> >-->
                                        <?php } ?>
                                    </div></td>
                                </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<script>
    /**
     * For tooltips
     */
    $('.ttip').tooltip({placement: 'top'});
</script>