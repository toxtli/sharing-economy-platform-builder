<?php
if(!defined('ABSPATH')) die('!');
// Setup wordpress URL prefix
$url = get_permalink(get_the_ID());
$sap = strpos($url, "?")?"&":"?";
$purl = $url.$sap;
?>
<div class="w3eden">
	<form class='form' method="post" action="<?php echo $purl ?>section=check-token">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-4 col-md-offset-4">
					<div class="panel panel-default">

						<div class="panel-heading">
							Check Query Status
						</div>

						<div class="panel-body">
							<div class='form-group'>
								<input class='form-control' type="text" placeholder="Enter your token here" name='token'/>

							</div>

						</div>
						<div class="panel-footer text-right">
							<button class='btn btn-primary btn-sm'>Check Status</button>
						</div>
					</div>
				</div>
			</div>

		</div> 
	</form>
</div>
