<?php
/**
* BreezingForms - A Joomla Forms Application
* @version 1.8
* @package BreezingForms
* @copyright (C) 2008-2012 by Markus Bopp
* @license Released under the terms of the GNU General Public License
**/
defined('_JEXEC') or die('Direct Access to this location is not allowed.');
?>

<?php if ($RECORD_ID != ''): ?>
<?php echo $PROCESS_RECORDSAVEDID?> <?php echo $RECORD_ID ?><?php echo $NL ?>
<?php endif; ?>

<?php echo $PROCESS_FORMID?>: <?php echo $FORM ?><?php echo $NL ?>
<?php echo $PROCESS_FORMTITLE ?>: <?php echo $TITLE ?><?php echo $NL ?>
<?php echo $PROCESS_FORMNAME ?>: <?php echo $NAME ?><?php echo $NL ?>
<?php echo $PROCESS_SUBMITTEDAT ?>: <?php echo $SUBMITTED ?><?php echo $NL ?>
<?php echo $PROCESS_SUBMITTERIP ?>: <?php echo $IP ?><?php echo $NL ?>
<?php echo $PROCESS_SUBMITTERID ?>: <?php echo $SUBMITTERID ?><?php echo $NL ?>
<?php echo $PROCESS_SUBMITTERUSERNAME ?>: <?php echo $SUBMITTERUSERNAME ?><?php echo $NL ?>
<?php echo $PROCESS_SUBMITTERFULLNAME ?>: <?php echo $SUBMITTERFULLNAME ?><?php echo $NL ?>
<?php echo $PROCESS_PROVIDER ?>: <?php echo $PROVIDER ?><?php echo $NL ?>
<?php echo $PROCESS_BROWSER ?>: <?php echo $BROWSER ?><?php echo $NL ?>
<?php echo $PROCESS_OPSYS ?>: <?php echo $OPSYS ?><?php echo $NL ?>

<?php foreach ($MAILDATA as $DATA): ?>
<?php echo $DATA[_FF_DATA_TITLE]?>: <?php echo $DATA[_FF_DATA_VALUE]?><?php echo $NL ?>
<?php endforeach; ?>