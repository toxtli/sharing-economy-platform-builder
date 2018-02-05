<?php if(!defined('ABSPATH')) die('!'); ?><div class='w3eden'>
	<div class="container">
		<div class='row'>
			<div class='col-md-6 col-md-offset-3'>
				<div class='panel panel-default'>
					<div class='panel-heading'>New Agent</div>
					<div class=panel-body>
						<form method="post" class='form' role='form' action="edit.php?post_type=form&page=add-agent&action=add_agent">
							<fieldset>
								<div class="form-group">
									<label for="agentname">Agent name</label>
									<input type='text' name='agentname' class='form-control' value="" placeholder="Enter your desired agent name" />
								</div>
								<div class="form-group row">
									<div class="col-md-6">
										<label for="password">Password</label>
										<input type='password' name='password' class='form-control' />
									</div>
									<div class="col-md-6">
										<label for="password_confirmation">Confirm password</label>
										<input type='password' name='password_confirmation' class='form-control' />
									</div>
								</div>
								<div class="form-group">
									<label for="email">Email</label>
									<input type='email' name='email' class='form-control' value="" placeholder="Enter your email address" />
								</div>
								<div class="pull-right">
									<button type='submit' class="btn btn-primary">Add agent</button>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>