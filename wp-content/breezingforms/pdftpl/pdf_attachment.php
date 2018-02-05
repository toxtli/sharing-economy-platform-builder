<?php
/**
* BreezingForms - A Joomla Forms Application
* @version 1.8
* @package BreezingForms
* @copyright (C) 2008-2012 by Markus Bopp
* @license Released under the terms of the GNU General Public License
**/
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
// IMPORTANT!
// Supported Tags: h1, h2, h3, h4, h5, h6, b, u, i, a, img, p, br, strong, em, font, blockquote, li, ul, ol, hr, td, th, tr, table, sup, sub, small
?>
<table border="1" width="100%">
<tr>
<td colspan="2" bgcolor="#cccccc" align="left" valign="middle">
	<h2><?php echo $this->submitted; ?></h2>
</td>
</tr>
<tr>
<td>
	<strong><?php echo BFText::_('COM_BREEZINGFORMS_ID') ?>:</strong>
</td>
<td>
	<?php echo $this->form; ?> 
</td>
</tr>
<tr>
<td>
	<strong><?php echo BFText::_('COM_BREEZINGFORMS_PROCESS_SUBMITTEDAT') ?>:</strong>
</td>
<td>
	<?php echo $this->submitted; ?> 
</td>
</tr>
<tr>
<td>
	<strong><?php echo BFText::_('COM_BREEZINGFORMS_IP') ?>:</strong>
</td>
<td>
	<?php echo $this->ip; ?> 
</td>
</tr>
<tr>
<td>
	<strong><?php echo BFText::_('COM_BREEZINGFORMS_BROWSER') ?>:</strong>
</td>
<td>
	<?php echo htmlentities($this->browser, ENT_QUOTES, 'UTF-8');  ?> 
</td>
</tr>
<tr>
<td>
	<strong><?php echo BFText::_('COM_BREEZINGFORMS_PROCESS_OPSYS') ?>:</strong>
</td>
<td>
	<?php echo htmlentities($this->opsys, ENT_QUOTES, 'UTF-8');  ?> 
</td>
</tr>
<tr>
<td colspan="2" bgcolor="#cccccc">
	<strong><?php echo BFText::_('COM_BREEZINGFORMS_DATA') ?>:</strong>
</td>
</tr>
<?php
if (count($xmldata)){
	foreach ($xmldata as $data) {
?>
<tr>
<td>
	<strong><?php echo wordwrap(htmlentities($data[_FF_DATA_TITLE], ENT_QUOTES, 'UTF-8'), 40, '<br />', true); ?>:</strong>
</td>
<td>
    <?php echo nl2br(htmlentities(substr(is_array($data[_FF_DATA_VALUE]) ? implode('|',$data[_FF_DATA_VALUE]) : $data[_FF_DATA_VALUE],0,10000), ENT_QUOTES, 'UTF-8')); ?>
</td>
</tr>
<?php
	}
}
?>
</table>