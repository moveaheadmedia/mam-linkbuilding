jQuery(document).ready(function ($) {
    // import existing resources
    $('.import-existing-resource').click(function(){
        var form = $(this).parent('form');
        var table = form.parent('div');
        var ajaxurl = mam_lb_plugin_object.ajax_url;
        var _data = form.serialize();
        table.addClass('cancelled').addClass('imported');
        jQuery.post(ajaxurl, _data, function (response) {
            table.append(response);
            doimportexistingall();
        });
    });
    var importexistingall = false;
    function doimportexistingall(){
        if(importexistingall){
            $('.existing-content .import-table-item:not(.cancelled)').first().find('.import-existing-resource').trigger('click');
        }
        $('.import-all-total').text($('.existing-content .import-table-item:not(.cancelled), .existing-content .import-table-item.imported').length);
        $('.import-all-completed').text($('.existing-content .import-table-item.imported').length);
    }
    doimportexistingall();
    $('body').on('click', '.import-existing-all', function(){
        importexistingall = true;
        $(this).val('STOP').addClass('stop-import-existing').removeClass('import-existing-all');
        doimportexistingall();
    });
    $('body').on('click', '.stop-import-existing', function(){
        importexistingall = false;
        $(this).val('IMPORT ALL').removeClass('stop-import-existing').addClass('import-existing-all');
    });

    // import new resources
    $('.import-new-resource').click(function(){
        var form = $(this).parent().parent().parent().parent().parent('form');
        var location = $(this).parent();
        var _tr = $(this).parent().parent();
        var ajaxurl = mam_lb_plugin_object.ajax_url;
        var _data = form.serialize();
        _tr.addClass('cancelled').addClass('imported');
        jQuery.post(ajaxurl, _data, function (response) {
            location.append(response);
            doimportnewall();
        });
    });


    var importnewall = false;
    function doimportnewall(){
        if(importnewall){
            $('.new-content tr:not(.cancelled) .import-new-resource').first().trigger('click');
        }
        $('.import-all-new-total').text($('.new-content tbody tr:not(.cancelled), .new-content tbody tr.imported').length);
        $('.import-all-new-completed').text($('.new-content tbody tr.imported').length);
    }
    doimportnewall();
    $('body').on('click', '.import-new-all', function(){
        importnewall = true;
        $(this).val('STOP').addClass('stop-import-new').removeClass('import-new-all');
        doimportnewall();
    });
    $('body').on('click', '.stop-import-new', function(){
        importnewall = false;
        $(this).val('IMPORT ALL').removeClass('stop-import-new').addClass('import-new-all');
    });

    // import existing order
    $('.import-existing-order').click(function(){
        var form = $(this).parent('form');
        var table = form.parent('div');
        var ajaxurl = mam_lb_plugin_object.ajax_url;
        var _data = form.serialize();
        table.addClass('cancelled').addClass('imported');
        jQuery.post(ajaxurl, _data, function (response) {
            table.append(response);
            doimportexistingorderall();
        });
    });

    var importexistingorderall = false;
    function doimportexistingorderall(){
        if(importexistingorderall){
            $('.existing-order-content .import-table-item:not(.cancelled)').find('.import-existing-order').first().trigger('click');
            console.log('importing existing orders.');
            console.log($('.existing-order-content .import-table-item:not(.cancelled)').find('.import-existing-order').first().text());
        }
        $('.import-all-existing-order-total').text($('.existing-order-content .import-table-item:not(.cancelled), .existing-order-content .import-table-item.imported').length);
        $('.import-all-existing-order-completed').text($('.existing-order-content .import-table-item.imported').length);
    }
    doimportexistingorderall();

    $('body').on('click', '.import-existing-order-all', function(){
        importexistingorderall = true;
        $(this).val('STOP').addClass('stop-import-existing-order').removeClass('import-existing-order-all');
        doimportexistingorderall();
    });
    $('body').on('click', '.stop-import-existing-order', function(){
        importexistingorderall = false;
        $(this).val('IMPORT ALL').removeClass('stop-import-existing-order').addClass('import-existing-order-all');
    });


    // import new order
    $('.import-new-order').click(function(){
        var form = $(this).parent().parent().parent().parent().parent('form');
        var location = $(this).parent();
        var _tr = $(this).parent().parent();
        var ajaxurl = mam_lb_plugin_object.ajax_url;
        var _data = form.serialize();
        _tr.addClass('cancelled').addClass('imported');
        jQuery.post(ajaxurl, _data, function (response) {
            location.append(response);
            doimportneworderall();
        });
    });
    var importneworderall = false;
    function doimportneworderall(){
        if(importneworderall){
            $('.new-order-content tr:not(.cancelled) .import-new-order').first().trigger('click');
        }
        $('.import-all-new-order-total').text($('.new-order-content tbody tr:not(.cancelled), .new-order-content tbody tr.imported').length);
        $('.import-all-new-order-completed').text($('.new-order-content tbody tr.imported').length);
    }
    doimportneworderall();
    $('body').on('click', '.import-new-order-all', function(){
        importneworderall = true;
        $(this).val('STOP').addClass('stop-import-new-order').removeClass('import-new-order-all');
        doimportneworderall();
    });
    $('body').on('click', '.stop-import-new-order', function(){
        importneworderall = false;
        $(this).val('IMPORT ALL').removeClass('stop-import-new-order').addClass('import-new-order-all');
    });

    // import existing and new accordions
    $('h2.existing-h1').click(function(){
        $('.existing-content').toggleClass('active');
        $('.new-content').removeClass('active');
        $('.new-order-content').removeClass('active');
        $('.existing-order-content').removeClass('active');
    });
    $('h2.new-h1').click(function(){
        $('.new-content').toggleClass('active');
        $('.existing-content').removeClass('active');
        $('.new-order-content').removeClass('active');
        $('.existing-order-content').removeClass('active');
    });
    $('h2.existing-order-h1').click(function(){
        $('.existing-order-content').toggleClass('active');
        $('.existing-content').removeClass('active');
        $('.new-content').removeClass('active');
        $('.new-order-content').removeClass('active');
    });
    $('h2.new-order-h1').click(function(){
        $('.new-order-content').toggleClass('active');
        $('.existing-content').removeClass('active');
        $('.new-content').removeClass('active');
        $('.existing-order-content').removeClass('active');
    });

    // Cancel import click
    $('.import-table-item .cancel').click(function(){
        var target = $(this).attr('data-target');
        $('[data-name="' + target + '"]').addClass('cancelled');
        doimportexistingall();
        doimportexistingorderall();
        doimportnewall();
        doimportneworderall();
    });

    // UNDO import click
    $('.import-table-item .undo').click(function(){
        var target = $(this).attr('data-target');
        $('[data-name="' + target + '"]').removeClass('cancelled');
        doimportexistingall();
        doimportexistingorderall();
        doimportnewall();
        doimportneworderall();
    })

    // Data Table
    $('.datatable tfoot th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text" placeholder="Search ' + title + '" />');
    });
    $('.datatable:not(.server)').DataTable({
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
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        } ],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        "paging": true
    });

    // Server processing
    $('.datatable.server').DataTable({
        initComplete: function () {
            // Apply the search
            this.api().columns().every(function () {
                var that = this;

                $('input', this.footer()).on('change', function () {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
        },
        dom: 'Bfrtip',
        "lengthMenu": [[50, 100, 250, 500, -1], [50, 100, 250, 500, "All"]],
        buttons: [
            'pageLength', 'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        } ],
        select: {
            style:    'os',
            selector: 'td:first-child'
        },
        "paging": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": mam_lb_plugin_object.site_url + "/mam-data-loader/",
            "type": "POST"
        },

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

    // Niche Search
    $('input#niche').on('change', function(){
        $('input[placeholder="Search Niche"]').val($('input#niche').val()).trigger('change');
    });

});