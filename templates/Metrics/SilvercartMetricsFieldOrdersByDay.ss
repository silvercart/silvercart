<h3>Bestellverlauf</h3>

<div id="chart1" style="height:300px; width:650px;"></div>

<script type="text/javascript">
    jQuery(document).ready(function () {
        var line1 = [
            ['2012-01-03 0:00PM', 4],
            ['2012-01-05 0:00PM', 6],
            ['2012-01-09 0:00PM', 9],
            ['2012-01-12 0:00PM', 6],
            ['2012-01-13 0:00PM', 12],
            ['2012-01-17 0:00PM', 17]
        ];

        var plot1 = jQuery.jqplot(
            'chart1',
            [line1],
            {
                title: 'Bestellverlauf tageweise',
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
                    borderColor: '#dddddd',
                    borderWidth: 1.0,
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

