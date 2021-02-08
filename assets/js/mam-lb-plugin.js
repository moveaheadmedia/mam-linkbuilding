jQuery(document).ready(function ($) {
    // Data Table
    $('.datatable tfoot th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text" placeholder="Search ' + title + '" />');
    });
    $('.datatable').DataTable({
        initComplete: function () {
            // Apply the search
            this.api().columns().every(function () {
                var that = this;

                $('input', this.footer()).on('keyup change clear', function () {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
        },
        dom: 'Bfrtip',
        buttons: [
            'pageLength', 'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        "paging": true,
    });

    // Columns sorting
    $("#sortable2").sortable().disableSelection();
    $("#sortable2").on("sortstop", function (event, ui) {
        let sortableValuesA = [];
        $('#sortable2 li').each(function (index) {
            sortableValuesA.push($(this).attr('data-value'));
        });
        $('input[name="resource-order"]').val(JSON.stringify(sortableValuesA));
    });
    $("#sortable2").trigger('sortstop');

    // reset columns
    $('.columns-form button[type="reset"]').click(function (event) {
        event.preventDefault();
        $('input[name="resource-order"]').val('');
        $('.columns-form').submit();
    });

    // Columns list
    $('#columnsList').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        var _val = ($(this).val());
        if (Array.isArray(_val)) {
            var html = '';
            $.each(_val, function (index, value) {
                html = html + '<li class="ui-state-default" data-value="' + value + '">' + value + '</li>';
            });
            $("#sortable2").html(html);
            $("#sortable2").sortable("refresh");
            $("#sortable2").trigger('sortstop');
        } else {
            $("#sortable2").html('');
            $("#sortable2").sortable("refresh");
            $("#sortable2").trigger('sortstop');
        }
    });

    // reset filters
    $('input[type="reset"]').click(function (event) {
        event.preventDefault();
        $("#da").val('0 - 100');
        $("#dr").val('0 - 100');
        $("#price").val('0 - 3000');
        $("#rd").val('0');
        $("#tr").val('0');
        $("#sectors").val('');
        $("#sectors").selectpicker("refresh");

        $('.filters').submit();
    });

    // Fullscreen
    $('body').on('click', '.enterfullscreen', function () {
        $(this).parent().parent().addClass('fullscreen');
        $('.fullscreen').fullScreen(true)
        return false;
    });
    $('body').on('click', '.existfullscreen', function () {
        $(this).parent().parent().removeClass('fullscreen');
        $(document).fullScreen(false);
        return false;
    });

    // Table view elipssies elemnts
    $('body').on('click', 'table.dataTable tbody th, table.dataTable tbody td', function () {
        $(this).toggleClass('active');
    });

    // Range Slider jQuery UI
    if ($('#daSlider').length) {
        $('#daSlider').slider({
            range: true,
            min: 0,
            max: 100,
            values: JSON.parse($("#da").attr('data-value')),
            slide: function (event, ui) {
                $("#da").val(ui.values[0] + " - " + ui.values[1]);
            }
        });
    }
    if ($('#drSlider').length) {
        $('#drSlider').slider({
            range: true,
            min: 0,
            max: 100,
            values: JSON.parse($("#dr").attr('data-value')),
            slide: function (event, ui) {
                $("#dr").val(ui.values[0] + " - " + ui.values[1]);
            }
        });
    }
    if ($('#priceSlider').length) {
        $('#priceSlider').slider({
            range: true,
            min: 0,
            max: 3000,
            values: JSON.parse($("#price").attr('data-value')),
            slide: function (event, ui) {
                $("#price").val(ui.values[0] + " - " + ui.values[1]);
            }
        });
    }
    if ($('#rdSlider').length) {
        $('#rdSlider').slider({
            range: "max",
            min: 0,
            max: 100,
            value: parseInt($("#rd").attr('data-value')),
            slide: function (event, ui) {
                $("#rd").val(ui.value);
            }
        });
    }
    if ($('#trSlider').length) {
        $('#trSlider').slider({
            range: "max",
            min: 0,
            max: 100,
            value: parseInt($("#tr").attr('data-value')),
            slide: function (event, ui) {
                $("#tr").val(ui.value);
            }
        });
    }

    // Date rangepicker
    $('.datesRange').daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'This Year': [moment().startOf('year'), moment().endOf('year')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        },
        'locale': {
            'format': 'DD MMMM YYYY'
        },
        "opens": "left",
        "maxDate": moment(),
        "alwaysShowCalendars": true
    }, function(start, end, label) {
        console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
    });

});