<?php
if(!defined('ABSPATH')) die('!');
print_error('article');
clear_error_msg('article');
print_msg('system');
clear_msg('system');
?>
<script type="text/javascript" src="<?php echo base_url('js/flot/jquery.flot.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/flot/jquery.flot.symbol.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/flot/jquery.flot.axislabels.js'); ?>"></script>

<script type="text/javascript" src="<?php echo base_url('js/flot/jquery.flot.stack.js'); ?>"></script>
<script type="text/javascript">
    $(function() {

        var d1 = <?php echo $stats['views'];?>;
        var d2 = <?php echo $stats['submits'];?>;



        $.plot("#placeholder", [
            {
                label: "Views",
                data: d1,
                bars: {
                    show: true,
                    barWidth: 20,
                    fill: true,
                    lineWidth: 1,
                    order: 1,
                    fillColor:  "#AA4643"
                },
                color: "#AA4643"
            },
            {
                label: "Submits",
                data: d2,
                bars: {
                    show: true,
                    barWidth: 20,
                    fill: true,
                    lineWidth: 1,
                    order: 2,
                    fillColor:  "#89A54E"
                },
                color: "#89A54E"
            }
        ],
            {
                xaxis:{ticks:<?php echo $stats['ticks'];?>},
                grid: {
                    hoverable: true,
                    clickable: false,
                    borderWidth: 1
                },
                series: {
                    shadowSize: 1
                }
            }

        );

        // Add the Flot version string to the footer

        $("#footer").prepend("Flot " + $.plot.version + " &ndash; ");
    });

</script>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Summery</b></div>
                <div class="panel-body np">
                    <table class="table table-striped" style="margin-bottom: 0">

                        <tbody>
                        <tr><td><?php echo "Total forms"; ?></td><td><?php echo $total_forms; ?></td></tr>
                        <tr><td><?php echo "Total new requests"; ?></td><td><?php echo $total_new_requests;?></td></tr>
                        <tr><td><?php echo "Total resolved requests"; ?></td><td><?php echo $total_resolved_requests; ?></td></tr>
                        <tr><td><?php echo "Total pending requests"; ?></td><td><?php echo $total_pending_requests; ?></td></tr>
                        <tr><td><?php echo "Total requests in progress"; ?></td><td><?php echo $total_inprogress_requests; ?></td></tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><b>Form Activity</b></div>
                <div class="panel-body">

                    <div id="placeholder" style="height: 147px"></div>

                </div>
            </div>
        </div>

    </div>

</div>
<script>
    $('.ttip').tooltip({placement:'bottom'});
</script>



<pre>
1. total forms
2. total new requests
3. total resolved request
4. total pending request
5. total request in progress

-----
1. form activity
2. daily activity graph
3. monthly activete
</pre>