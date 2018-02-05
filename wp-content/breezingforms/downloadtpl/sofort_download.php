<?php
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
/**
* BreezingForms - A Joomla Forms Application
* @version 1.8
* @package BreezingForms
* @copyright (C) 2008-2012 by Markus Bopp
* @license Released under the terms of the GNU General Public License
**/
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
?>
<?php echo BFText::_('COM_BREEZINGFORMS_THANK_YOU_FOR_BUYING'); ?>
<br/>
<br/>
<?php echo BFText::_('COM_BREEZINGFORMS_YOUR_TRANSACTION_ID')  ?>: <?php echo $tx_token; ?>
<br/>
<?php echo BFText::_('COM_BREEZINGFORMS_PAYMENT_METHOD_SU')  ?>
<br/>
<br/>
<?php
if($confirmed){
?>
<a href="<?php echo JURI::root() ?>index.php?raw=true&option=com_breezingforms&amp;sofortueberweisungDownload=true&amp;tx=<?php echo $tx_token ?>&amp;form=<?php echo $formId ?>&amp;record_id=<?php echo $recordId ?>"><?php echo BFText::_('COM_BREEZINGFORMS_DOWNLOAD'); ?> (<?php echo BFText::_('COM_BREEZINGFORMS_ALLOWED_TRIES'); ?>: <?php echo $tries ?>)</a>
<?php
} else {
?>
<?php echo BFText::_('COM_BREEZINGFORMS_YOUR_PAYMENT_REQUIRES_CONFIRMATION')  ?>
<script>
setTimeout("location.reload()",3000);
</script>
<?php
}
?>