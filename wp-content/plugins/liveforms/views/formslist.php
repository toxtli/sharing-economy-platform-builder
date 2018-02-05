<?php if(!defined('ABSPATH')) die('!'); ?><div class='w3eden'>
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading"><b>My Forms</b></div>
			<div class="panel-body np">

				<table class="table">
					<thead>
						<tr>
							<th width="30px">ID</th>
							<th>Title</th>
							<th width="250px" class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($forms as $id => $form): ?>
							<tr>
								<td><?php echo $form->ID; ?></td>
								<td><?php echo $form->post_title; ?></td>
								<td class="text-right"><a rel="<?php echo $form->ID; ?>" data-toggle="modal"
														  data-target="#modal" href="#"
														  class="btn-code btn btn-info btn-xs ttip" title="Embed Code"><i
											class="fa fa-code"></i></a> <a
										href="?section=contact-forms&view=assign_agents&form=<?php echo $form->ID ?>"
										class="btn btn-warning btn-xs ttip" title="Assign agents"><i class="fa fa-user"></i></a>
									<a href="?section=contact-forms&view=edit&form=<?php echo $form->ID ?>"
									   class="btn btn-primary btn-xs ttip" title="Edit Form"><i class="fa fa-pencil"></i></a> <a
										href="?section=contact-forms&action=remove&id=<?php echo $form->ID ?>"
										class="btn btn-danger btn-xs ttip" onclick="return confirm('Are you sure?');"
										title="Delete Form"><i class="fa fa-times"></i></a> <a
										href="<?php $form->ID ?>/"
										data-toggle="modal" data-target="#modal" class="btn btn-default btn-xs ttip btn-preview"
										title="Preview Form"><i class="fa fa-desktop"></i></a> <a
										href="?section=contact-forms&view=showreqs&form=<?php echo $form->ID ?>"
										class="btn btn-default btn-xs ttip" title="Show form data"><i
											class="fa fa-list"></i></a></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

			</div>
		</div>

	</div>
</div>
<script>
	jQuery(function($) {
		$('.ttip').tooltip({placement: 'bottom'});
		$(function() {
			$('.btn-preview').on('click', function(e) {
				e.preventDefault();

				$('#modal .modal-title').html('From Preview');
				$('#modal .modal-body').css('background', '#ffffff').html("<div class='data-loading'><i class='fa fa-spinner fa-spin'></i> &nbsp; loading...</div>").load(this.href);
				$('#modal').modal('show');
				return false;
			});
			$('.btn-code').on('click', function() {

				$('#modal .modal-title').html('Embed Code');
				var formid = this.rel;
				fields_html = 'Fields';
				//fields_html += "<fieldset>";
				fields_html += "<table ><tbody>"
				fields_html += "<tr><td><input type='text' id='imgurlval' rel='" + formid + "'  class='changeval form-control' placeholder='Button Image URL'/></td></tr>";
				fields_html += "<tr><td><input type='text' id='heightval' rel='" + formid + "' class='changeval form-control' placeholder='Form Height'/></td></tr>";
				fields_html += "<tr><td><input type='text' id='widthval' rel='" + formid + "' class='changeval form-control' placeholder='Form Width'/></td></tr>";
				fields_html += "</tbody></table>";
				//fields_html += '</fieldset></div></div></div>';

				popup_html_head = "<textarea id='popup_script' class='form-control' rows='5' readonly>&lt;script src='<?php echo 'base_url'; ?>/js/liveform.js'>&lt;/script>\r\n&lt;script>\r\nvar options = { formid:" + this.rel + ",\r\ntype:1,\r\nwidth:300,\r\nheight:400,\r\nbutton_image:'";
				popup_html_head1 = "<textarea id='popup_script' class='form-control' rows='5' readonly>&lt;script src='<?php echo 'base_url'; ?>/js/liveform.js'>&lt;/script>\r\n&lt;script>\r\nvar options = { formid:" + this.rel + ",\r\ntype:2,\r\nwidth:300,\r\nheight:400,\r\nbutton_image:'";
				popup_html_base = "imageurl";
				popup_html_tail = "' }; \r\nLiveForm.render(options, '<?php echo 'base_url' . '/' ?>');  &lt;/script></textarea>";

				html = '<ul class="nav nav-tabs ectabs">' +
						'<li class="active"><a href="#pec" data-toggle="tab">Popup</a></li>' +
						'<li><a href="#iec" data-toggle="tab">Inline JS</a></li>' +
						'<li><a href="#iecf" data-toggle="tab">iFrame</a></li>' +
						'<li><a href="#iecu" data-toggle="tab">URL</a></li>' +
						/*                '<li><a href="#wp" data-toggle="tab">WP</a></li>' +
						 '<li><a href="#jm" data-toggle="tab">Joomla</a></li>' +*/
						'</ul>';
				html += '<div class="tab-content" style="padding:0;border:0;">';
				html += "<div class='tab-pane active fade in' id='pec'><div class='container'><div class='row'><div class='col-md-2'>" + fields_html + "</div><div class='col-md-3'>Copy/Paste the following code" + popup_html_head + popup_html_base + popup_html_tail + "</div></div></div></div>";
				html += "<div class='tab-pane' id='iec'>Copy/Paste the following code" + popup_html_head1 + popup_html_base + popup_html_tail + "</div>";
				html += '<div class="tab-pane" id="iecf"><textarea class="form-control" rows="5" readonly>&lt;iframe src="<?php echo 'base_url' ?>/' + this.rel + '/" style="width:100%;height:600px;border:0;">&lt;/iframe></textarea></div>';
				html += '<div class="tab-pane" id="iecu"><textarea class="form-control" rows="5" readonly><?php echo 'base_url' ?>/form/' + this.rel + '/?fullview=1</textarea></div>';
				//html += '<div class="tab-pane" id="wp"><textarea class="form-control" rows="5" readonly>[liveform formid="'+this.rel+'"]</textarea> </div>';
				//html += '<div class="tab-pane" id="jm"><textarea class="form-control" rows="5" readonly>coming soon</textarea> </div>';
				html += '</div>';

				$('#modal .modal-body').css('background', '#ffffff').html(html);
				$('.modal-content textarea').on("click", function() {
					$(this).select();
				});

				/*
				 To change the script on-the-fly
				 while values are entered
				 */
				$('.changeval').keyup(function() {
					var img = ($('#imgurlval').val() == '' ? 'button_image' : $('#imgurlval').val());
					var formid = $(this).attr('rel');   // Fetching the form-id
					var width = ($('#widthval').val() == '' ? 200 : $('#widthval').val());        // 200 is default value of width
					var height = ($('#heightval').val() == '' ? 400 : $('#heightval').val());     // 300 is default value of height

					popup_html_head = "&lt;script src='<?php echo 'base_url' ?>/js/liveform.js'>&lt;/script>\r\n&lt;script>\r\nvar options = { formid:" + formid + ",\r\ntype:1,\r\nwidth:" + width + ",\r\nheight:" + height + ",\r\nbutton_image:'";
					popup_html_base = img;
					popup_html_tail = "' }; \r\nLiveForm.render(options, '<?php echo 'base_url' . '/' ?>');  &lt;/script>";
					$('#popup_script').html(popup_html_head + img + popup_html_tail);
					$('#popup_script').html(popup_html_head + img + popup_html_tail);
				});
			});

		});

	});



</script>