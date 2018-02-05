<?php if(!defined('ABSPATH')) die('!'); ?><link rel="stylesheet" href="<?php echo LF_BASE_URL ?>views/css/morris-0.4.3.min.css">
<script src="<?php echo LF_BASE_URL ?>views/js/raphael-min.js"></script>
<script src="<?php echo LF_BASE_URL ?>views/js/morris-0.4.3.min.js"></script>
<div class="wrap">
	<div class="w3eden">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-4">
							<?php foreach ($stats as $stat) { ?>
								<div class="panel panel-default">
									<div class="panel-heading">
										<?php echo $stat['label'] ?>
									</div>
									<div class="panel-body">
										<?php echo $stat['value']['label'] ?>: <span class="pull-right label label-success"><?php echo $stat['value']['value'] ?></span>
									</div>
								</div>
							<?php } ?>
							<div class="panel panel-default">
								<div class="panel-heading">
									Choose form
								</div>
								<div class="panel-body">
									<?php //<form class="form" id="form-selector" action=""> ?>
										<div class="row">
											<div class="col-md-9">
												<select class="form-control" name="specific_form" id="sp-form">
													<option <?php if ($selected_form_id == 'none') echo 'selected="selected"' ?> value="none">Select a form for it's stats</option>
													<?php foreach ($form_ids as $form_id => $form_title) { ?>
														<option <?php if ($selected_form_id == $form_id) echo 'selected="selected"' ?> value='<?php echo $form_id ?>'><?php echo $form_title ?></option>
													<?php } ?>
												</select>
											</div>
											<div class="col-md-3">
												<button class="btn btn-info pull-right" name="form-selected" id="form-selected">View</button>
											</div>
										</div>
									<?php //</form> ?>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div class="row">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Graph	
									</div>
									<div class="panel-body">
										<div id="graph">

										</div>
									</div>
									<div class="panel-footer">
										<div class="row">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-6">
														<label>From: </label>
														<input class='form-control datepicker' type="text"	id="picker-from"/>
													</div>
													<div class="col-md-6">
														<label>To: </label>
														<input class='form-control datepicker' type="text"	id="picker-to"/>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" id="stats-data">
	jQuery(document).ready(function($) {
		var submits_data = JSON.parse('<?php echo $submits ?>');
		var views_data = JSON.parse('<?php echo $views ?>');
		var morris_graph = new Morris.Line({
							element: 'graph',
							data: get_initial_graph_data(submits_data, views_data, $('#sp-form').val(), 'year'),
							xkey: 'year',
							ykeys: ['views','submits'],
							labels: ['Views','Submits']
						});


		$('.datepicker').on('change', function(){
			date1Value = extract_date($('#picker-from').val(), 'dd-mm-yy');
			date2Value = extract_date($('#picker-to').val(), 'dd-mm-yy');
			
			if (date1Value <= date2Value) {
				// here be dragons
				gdata = get_range_data(submits_data, views_data, date1Value, date2Value, $('#sp-form').val(), 'day');
				
				$('#graph').fadeOut();
				$('#graph').html('');
				$('#graph').fadeIn();

				var new_morris_data = {
					element: 'graph',
					data: gdata,
					xkey: 'day',
					ykeys: ['views', 'submits'],
					labels: ['Views', 'Submits']
				}

				morris_graph = new Morris.Line(new_morris_data);

			}
		}) 

		$('#picker-from').datepicker({
			dateFormat: 'dd-mm-yy'
		});
		$('#picker-to').datepicker({
			dateFormat: 'dd-mm-yy'
		});

		$('#form-selected').on('click', function(){
			var gdata = get_initial_graph_data(submits_data, views_data, $('#sp-form').val(), 'year');
			$('#graph').fadeOut();
			$('#graph').html('');
			$('#graph').fadeIn();
			var new_morris_data = {
				element: 'graph',
				data: gdata,
				xkey: 'year',
				ykeys: ['views', 'submits'],
				labels: ['Views', 'Submits']
			}
			morris_graph = new Morris.Line(new_morris_data);
		});

		function extract_date(date, format) {
			$parts = date.split("-");
			$fparts = format.split("-");
			fdate = {
				day: $parts[$fparts.indexOf('dd')],
				month: $parts[$fparts.indexOf('mm')],
				year: $parts[$fparts.indexOf('yy')]
			}
				
			return Date.parse(fdate.year + '-' + fdate.month + '-' + fdate.day);
		}

		function prepare_data(period, data_set) {
			counter = {};
			for (var form_id in data_set) {
				form_stats = data_set[form_id];
				for (var index in form_stats) {
					p = form_stats[index]['time'][period];
					if (p in counter) {
						counter[p]++;
					} else {
						counter[p] = 1;
					}
				}
			}

			var data = [];
			index = 0;
			for (var p in counter) {
				var v = {};
				v[period] = p;
				v['value'] = counter[p];
				data[index++] = v;
			}


			return (data);
		}

		function get_initial_graph_data(data_set1, data_set2, form, period) {
			console.log(data_set1)
			console.log(data_set2)
			counter1 = {};
			counter2 = {};
			if (form == 'none') { // Form not selected
				for (var form_id in data_set1) {
					form_stats = data_set1[form_id];
					for (var index in form_stats) {
						p = form_stats[index]['time'][period];
						if (p in counter1) {
							counter1[p]++;
						} else {
							counter1[p] = 1;
						}
					}
				}
				for (var form_id in data_set2) {
					form_stats = data_set2[form_id];
					for (var index in form_stats) {
						p = form_stats[index]['time'][period];
						if (p in counter2) {
							counter2[p]++;
						} else {
							counter2[p] = 1;
						}
					}
				}

				var data = [];
				index = 0;
				for (var p in counter2) {
					var v = {};
					v[period] = p;
					v['views'] = counter2[p];
					if (p in counter1) {
						v['submits'] = counter1[p];
					} else {
						v['submits'] = null;
					}
					data[index++] = v;
				}


				return (data);
			} else {
				for (var form_id in data_set1) {
					if (form_id == form) {
						form_stats = data_set1[form_id];
						for (var index in form_stats) {
							p = form_stats[index]['time'][period];
							if (p in counter1) {
								counter1[p]++;
							} else {
								counter1[p] = 1;
							}
						}
					}
					
				}
				for (var form_id in data_set2) {
					if (form_id == form) {
						form_stats = data_set2[form_id];
						for (var index in form_stats) {
							p = form_stats[index]['time'][period];
							if (p in counter2) {
								counter2[p]++;
							} else {
								counter2[p] = 1;
							}
						}
					}
				}

				var data = [];
				
				index = 0;
				for (var p in counter2) {
					var v = {};
					v[period] = p;
					v['views'] = counter2[p];
					if (p in counter1) {
						v['submits'] = counter1[p];
					} else {
						v['submits'] = null;
					}
					data[index++] = v;
				}

				return (data);
			}
				
		}

		function get_range_data(data_set1, data_set2, date1, date2, form, period) {
			counter1 = {};
			counter2 = {};
			if (form == 'none') { // Form not selected
				for (var form_id in data_set1) {
					form_stats = data_set1[form_id];
					for (var index in form_stats) {
						p = form_stats[index]['time'][period];
						v = extract_date(p, 'yy-mm-dd');
						if (v>=date1 && v<=date2) {
							if (p in counter1) {
								counter1[p]++;
							} else {
								counter1[p] = 1;
							}
						}
					}
				}
				for (var form_id in data_set2) {
					form_stats = data_set2[form_id];
					for (var index in form_stats) {
						p = form_stats[index]['time'][period];
						v = extract_date(p, 'yy-mm-dd');
						if (v>=date1 && v<=date2) {
							if (p in counter2) {
								counter2[p]++;
							} else {
								counter2[p] = 1;
							}
						}
					}
				}

				var data = [];
				index = 0;
				for (var p in counter2) {
					var v = {};
					v[period] = p;
					v['views'] = counter2[p];
					if (p in counter1) {
						v['submits'] = counter1[p];
					} else {
						v['submits'] = null;
					}
					data[index++] = v;
				}

				return (data);
			} else {
				for (var form_id in data_set1) {
					if (form_id == form) {
						form_stats = data_set1[form_id];
						for (var index in form_stats) {
							p = form_stats[index]['time'][period];
							v = extract_date(p, 'yy-mm-dd');
							if (v>=date1 && v<=date2) {
								if (p in counter1) {
									counter1[p]++;
								} else {
									counter1[p] = 1;
								}
							}
						}
					}
					
				}
				for (var form_id in data_set2) {
					if (form_id == form) {
						form_stats = data_set2[form_id];
						for (var index in form_stats) {
							p = form_stats[index]['time'][period];
							v = extract_date(p, 'yy-mm-dd');
							if (v>=date1 && v<=date2) {
								if (p in counter2) {
									counter2[p]++;
								} else {
									counter2[p] = 1;
								}
							}
						}
					}
				}

				var data = [];
				
				index = 0;
				for (var p in counter2) {
					var v = {};
					v[period] = p;
					v['views'] = counter2[p];
					if (p in counter1) {
						v['submits'] = counter1[p];
					} else {
						v['submits'] = null;
					}
					data[index++] = v;
				}

				return (data);
			}
				
		}
	});


	
</script>