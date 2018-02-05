jQuery( document ).ready( function($) {
		function showTooltip( x, y, contents ) {
		$( '<div class="chart-tooltip">' + contents + '</div>' ).css( {
			top: y - 16,
			left: x + 20
		}).appendTo( 'body' ).fadeIn( 200 );
	}

	var prev_data_index = null;
	var prev_series_index = null;

	$( '.chart-placeholder' ).bind( 'plothover', function ( event, pos, item ) {
		if ( item ) {
			if ( prev_data_index !== item.dataIndex || prev_series_index !== item.seriesIndex ) {
				prev_data_index   = item.dataIndex;
				prev_series_index = item.seriesIndex;

				$( '.chart-tooltip' ).remove();

				if ( item.series.points.show || item.series.enable_tooltip ) {

					var y = item.series.data[item.dataIndex][1],
						tooltip_content = '';

					if ( item.series.prepend_label ) {
						tooltip_content = tooltip_content + item.series.label + ': ';
					}

					if ( item.series.prepend_tooltip ) {
						tooltip_content = tooltip_content + item.series.prepend_tooltip;
					}

					tooltip_content = tooltip_content + y;

					if ( item.series.append_tooltip ) {
						tooltip_content = tooltip_content + item.series.append_tooltip;
					}

					if ( item.series.pie.show ) {
						showTooltip( pos.pageX, pos.pageY, tooltip_content );
					} else {
						showTooltip( item.pageX, item.pageY, tooltip_content );
					}
				}
			}
		} else {
			$( '.chart-tooltip' ).remove();
			prev_data_index = null;
		}
	});
		
	
  $( '.wc_sparkline.bars' ).each( function() {
		var chart_data = $( this ).data( 'sparkline' );

		var options = {
			grid: {
				show: false
			}
		};

		// main series
		var series = [{
			data: chart_data,
			color: $( this ).data( 'color' ),
			bars: {
				fillColor: $( this ).data( 'color' ),
				fill: true,
				show: true,
				lineWidth: 1,
				barWidth: $( this ).data( 'barwidth' ),
				align: 'center'
			},
			shadowSize: 0
		}];

		// draw the sparkline
		$.plot( $( this ), series, options );
	});

	$( '.wc_sparkline.lines' ).each( function() {
		var chart_data = $( this ).data( 'sparkline' );

		var options = {
			grid: {
				show: false
			}
		};

		// main series
		var series = [{
			data: chart_data,
			color: $( this ).data( 'color' ),
			lines: {
				fill: false,
				show: true,
				lineWidth: 1,
				align: 'center'
			},
			shadowSize: 0
		}];

		// draw the sparkline
		$.plot( $( this ), series, options );
	});
} );