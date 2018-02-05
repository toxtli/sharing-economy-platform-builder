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
<?php echo BFText::_('COM_BREEZINGFORMS_PAYMENT_ERROR_MSG'); ?>
<br/>
<br/>
<?php echo BFText::_('COM_BREEZINGFORMS_YOUR_TRANSACTION_ID')  ?>: <?php echo $tx_token; ?>
<br/>
<br/>
<?php echo BFText::_('COM_BREEZINGFORMS_ERROR')  ?>: <?php echo htmlentities($msg); ?>