<h3>$FieldHeadline</h3>

<% if HasOrderLine %>
    <div id="chart1" style="height:300px; width:650px;"></div>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            var line1 = [
                $OrderLine
            ];

            var SilvercartMetricsFieldOrdersByDay = jQuery.jqplot(
                'chart1',
                [line1],
                {
                    title: '$ChartHeadline',
                    axes:  {
                        xaxis: {
                            renderer: jQuery.jqplot.DateAxisRenderer,
                            tickOptions: {
                                formatString: '%b&nbsp;%#d'
                            }
                        },
                        yaxis: {
                            tickOptions: {
                                formatString: '%d'
                            }
                        }
                    },
                    series: [
                        {
                            color: '#1C587A',
                            lineWidth: 1.5,
                            shadow: false,
                            markerOptions: {
                                style: 'filledCircle',
                                shadow: false
                            }
                        }
                    ],
                    grid: {
                        background: '#fbfbfb',
                        gridLineColor: '#eeeeee',
                        borderColor: '#999999',
                        borderWidth: 0.6,
                        shadow: false
                    },
                    highlighter: {
                        show: true,
                        sizeAdjust: 7.5
                    },
                    cursor: {
                        show: false
                    }
                }
            );
        });
    </script>
<% else %>
    <p><% _t('SilvercartMetricsFieldOrdersByDay.NO_ORDERS_YET') %></p>
<% end_if %>
