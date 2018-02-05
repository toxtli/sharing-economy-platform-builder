<?php if(!defined('ABSPATH')) die('!'); ?>
        <table class='table table-striped table-hover'>
            <thead><tr><th><input id="fic" type='checkbox' /></th><th>Action</th><th>Token</th><th>Time</th>
<?php
$fieldids = array();

foreach($fields as $id=>$field){
    $fieldids[] = $id;
    echo "<th>{$field['label']}</th>";
}
?>
</tr></thead><tbody>
<?php foreach($reqlist as $req){
	$time = date('d-m-Y', $req['time']);
echo "<tr id='fer_{$id}'><td><input type='checkbox' class='fic' name='ids[]' value='{$req['id']}' /></td><td><a href='?section=contact-forms&view=reply_req&form_id={$form['id']}&req_id={$req['id']}' class='btn btn-info btn-xs'><i class='fa fa-eye'></i> View</a></td><td>{$req['token']}</td><td>{$time}</td>";
    $req = unserialize($req['data']);
    foreach($fieldids as $id){
        $value = isset($req[$id])?$req[$id]:'';
        $value = is_array($value)?implode(", ", $value):$value;
        $value = esc_attr($value);
        echo "<td>{$value}&nbsp;</td>";
    }
echo "</tr>";
}
  ?>

            </tbody></table>
