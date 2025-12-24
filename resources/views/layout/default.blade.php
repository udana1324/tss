{{--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 4 & Angular 8
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
 --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" {{ Metronic::printAttrs('html') }} {{ Metronic::printClasses('html') }}>
    <head>
        <meta charset="utf-8"/>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        {{-- Title Section --}}
        <title>{{ config('app.name') }}</title>

        {{-- Meta Data --}}
        <meta name="description" content="@yield('page_description', $page_description ?? '')"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

        {{-- Favicon --}}
        <link rel="shortcut icon" href="{{ asset('media/logos/sata.ico') }}" />

        {{-- Fonts --}}
        {{ Metronic::getGoogleFontsInclude() }}

        {{-- Global Theme Styles (used by all pages) --}}
        @foreach(config('layout.resources.css') as $style)
            <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($style)) : asset($style) }}" rel="stylesheet" type="text/css"/>
        @endforeach

        {{-- Layout Themes (used by all pages) --}}
        @foreach (Metronic::initThemes() as $theme)
            <link href="{{ config('layout.self.rtl') ? asset(Metronic::rtlCssPath($theme)) : asset($theme) }}" rel="stylesheet" type="text/css"/>
        @endforeach

        {{-- Includable CSS --}}
        <style>
            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
            }

            /* Firefox */
            input[type=number] {
              -moz-appearance: textfield;
            }
        </style>
        @yield('styles')
    </head>

    <body {{ Metronic::printAttrs('body') }} {{ Metronic::printClasses('body') }}>

        @if (config('layout.page-loader.type') != '')
            @include('layout.partials._page-loader')
        @endif

        @include('layout.base._layout')

        {{-- Global Config (global config for global JS scripts) --}}
        <script>
            var KTAppSettings = {!! json_encode(config('layout.js'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) !!};
        </script>

        {{-- Global Theme JS Bundle (used by all pages)  --}}
        @foreach(config('layout.resources.js') as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach

        <script type="text/javascript">
            KTApp.blockPage({
                opacity: 0.5,
                overlayColor: '#012380',
                state: 'primary',
                message: 'Tunggu sebentar...'
            });

            $(document).ready(function() {
                KTApp.unblockPage();
            });

            var url = window.location.href;

            if (!url.includes("dashboard")) {

                $(document).ajaxStart(function() {
                    // show loader on start
                    KTApp.blockPage({
                        opacity: 0.5,
                        overlayColor: '#012380',
                        state: 'primary',
                        message: 'Tunggu sebentar...'
                    });
                }).ajaxStop(function() {
                    // hide loader on success
                    KTApp.unblockPage();
                });
            }

            $(document).ready(function() {
                // Disable Mouse scrolling
                $('input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
                // Disable keyboard scrolling
                $('input[type=number]').on('keydown',function(e) {
                    var key = e.charCode || e.keyCode;
                    // Disable Up and Down Arrows on Keyboard
                    if(key == 38 || key == 40 ) {
                        e.preventDefault();
                    }
                    else {
                        return;
                    }
                });
                var url = window.location.href;
                if (url.includes("dashboard")) {
                    // getOmzetData("Bulanan");
                    // getProfitData("Bulanan");
                }
            });

            function validasiTelp(evt) {
                var charCode = (evt.which) ? evt.which : event.keyCode;

                if (charCode != 43 && charCode != 40 && charCode != 41 && charCode != 46 && charCode != 45 &&
                charCode > 31 && (charCode < 48 || charCode > 57))
                    return false;

                return true;
            }

            function validasiAngka(evt) {
                var charCode = (evt.which) ? evt.which : event.keyCode
                    if (charCode > 31 && (charCode < 48 || charCode > 57)) {

                        return false;
                    return true;
                    }
                    else if (evt.which == 46 || evt.keyCode == 46) {
                        e.preventDefault();
                    }
                    else if (evt.which == 45 || evt.keyCode == 45) {
                        e.preventDefault();
                    }
                    else if (evt.which == 44 || evt.keyCode == 44) {
                        e.preventDefault();
                    }
                    else if (evt.which == 43 || evt.keyCode == 43) {
                        e.preventDefault();
                    }
            }

            function validasiDecimal(el, evt) {
                var charCode = (evt.which) ? evt.which : event.keyCode;
                var number = el.value.split(',');
                if (charCode != 44 && charCode > 31 && (charCode < 48 || charCode > 57)) {
                    return false;
                }
                //just one dot
                if(number.length>1 && charCode == 44){
                    return false;
                }
                //get the carat position
                var caratPos = getSelectionStart(el);
                var dotPos = el.value.indexOf(",");
                if( caratPos > dotPos && dotPos>-1 && (number[1].length > 1)){
                    return false;
                }
                return true;
            }



            //thanks: http://javascript.nwbox.com/cursor_position/
            function getSelectionStart(o) {
                if (o.createTextRange) {
                    var r = document.selection.createRange().duplicate()
                    r.moveEnd('character', o.value.length)
                    if (r.text == '') return o.value.length
                    return o.value.lastIndexOf(r.text)
                } else return o.selectionStart
            }

            $.fn.datepicker.dates['id'] = {
                days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
                daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                daysMin: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
                months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
                monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
                today: "Hari Ini",
                clear: "Clear",
                format: "yyyy-mm-dd",
                titleFormat: "MM yyyy", /* Leverages same syntax as ‘format’ */
                weekStart: 0
            };

            $(document).ready(function(){
                $.fn.datepicker.defaults.language = 'id';
                var parentMenu = "{{ $parent ?? '' }}";
                if (parentMenu != "") {
                    $("." + parentMenu).addClass("menu-item-open");
                }

            });

            function ExportExcel(removeLast, id, nm, type, fn, dl) {
                var element = $("#"+id).find("table").get(0);
                var elt = element.cloneNode(true);

                if (removeLast == "T") {
                    var row = elt.rows;
                    var i = row[0].cells.length - 1;
                    for (var j = 0; j < row.length; j++) {
                        // Deleting the ith cell of each row.
                        row[j].deleteCell(i);
                    }
                }

                var wb = XLSX.utils.table_to_book(elt, {sheet:"Data"});
                return dl ?
                    XLSX.write(wb, {bookType:type, bookSST:true, type: 'base64'}) :
                    XLSX.writeFile(wb, fn || (nm + '.' + (type || 'xlsx')));
            }

            $("#closeSTB").on('click', function() {
                $("#stickyToolBar").attr("class","d-none");
            });

            //Widget 1 Chart Init
            $(document).ready(function() {
                var url = window.location.href;
                if (url.includes("dashboard")) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/chartSales",
                        method: 'POST',
                        data: {

                        },
                        success: function(result){
                            if (result.length > 0) {
                                var arrayBulan = [];
                                var arrayNominal =[];

                                for (var i = 0; i < result.length; i++) {
                                    arrayBulan.push(result[i].indx);
                                    arrayNominal.push(parseFloat(result[i].nominal));
                                }

                                var element = document.getElementById("kt_mixed_widget_1_chart_custom_verstand");
                                var height = parseInt(KTUtil.css(element, 'height'));
                                var strokeColor = '#D13647';
                                var options = {
                                    series: [{
                                        name: 'Penjualan',
                                        data: arrayNominal,
                                    }],
                                    chart: {
                                        type: 'area',
                                        height: height,
                                        toolbar: {
                                            show: false
                                        },
                                        zoom: {
                                            enabled: false
                                        },
                                        sparkline: {
                                            enabled: true
                                        },
                                        dropShadow: {
                                            enabled: true,
                                            enabledOnSeries: undefined,
                                            top: 5,
                                            left: 0,
                                            blur: 3,
                                            color: strokeColor,
                                            opacity: 0.5
                                        }
                                    },
                                    plotOptions: {},
                                    legend: {
                                        show: false
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    fill: {
                                        type: 'solid',
                                        opacity: 0
                                    },
                                    stroke: {
                                        curve: 'smooth',
                                        show: true,
                                        width: 3,
                                        colors: [strokeColor]
                                    },
                                    xaxis: {
                                        categories: arrayBulan,
                                        // axisBorder: {
                                        //     show: false,
                                        // },
                                        // axisTicks: {
                                        //     show: false
                                        // },
                                        // labels: {
                                        //     show: false,
                                        //     style: {
                                        //         colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                                        //         fontSize: '12px',
                                        //         fontFamily: KTApp.getSettings()['font-family']
                                        //     }
                                        // },
                                        // crosshairs: {
                                        //     show: false,
                                        //     position: 'front',
                                        //     stroke: {
                                        //         color: KTApp.getSettings()['colors']['gray']['gray-300'],
                                        //         width: 1,
                                        //         dashArray: 3
                                        //     }
                                        // }
                                    },
                                    // yaxis: {
                                    //     min: 0,
                                    //     max: 200,
                                    //     labels: {
                                    //         show: false,
                                    //         style: {
                                    //             colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                                    //             fontSize: '12px',
                                    //             fontFamily: KTApp.getSettings()['font-family']
                                    //         }
                                    //     }
                                    // },
                                    states: {
                                        normal: {
                                            filter: {
                                                type: 'none',
                                                value: 0
                                            }
                                        },
                                        hover: {
                                            filter: {
                                                type: 'none',
                                                value: 0
                                            }
                                        },
                                        active: {
                                            allowMultipleDataPointsSelection: false,
                                            filter: {
                                                type: 'none',
                                                value: 0
                                            }
                                        }
                                    },
                                    tooltip: {
                                        style: {
                                            fontSize: '12px',
                                            fontFamily: KTApp.getSettings()['font-family']
                                        },
                                        y: {
                                            formatter: function (val) {
                                                return "Rp " + parseFloat(val).toLocaleString('id-ID', { maximumFractionDigits: 2})
                                            }
                                        },
                                        marker: {
                                            show: false
                                        }
                                    },
                                    colors: ['transparent'],
                                    markers: {
                                        colors: [KTApp.getSettings()['colors']['theme']['light']['danger']],
                                        strokeColor: [strokeColor],
                                        strokeWidth: 3
                                    }
                                };
                                var chart = new ApexCharts(element, options);
                                chart.render();
                            }
                        }
                    });
                }
            });

            //Widget 2 Sales Chart Init
            function getOmzetData(jenisPeriode) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/chartOmzet",
                    method: 'POST',
                    data: {
                        jenis_periode: jenisPeriode
                    },
                    success: function(result){
                        if (result.length > 0) {
                            var arrayTxt = [];
                            var arrayNominal =[];
                            var omzet = 0;

                            var sortedArray = result.sort(function(a,b) {
                                return new Date(a.nm) - new Date(b.nm)
                            });

                            for (var i = 0; i < sortedArray.length; i++) {
                                if (jenisPeriode == "Bulanan") {
                                    arrayTxt.push(moment(sortedArray[i].nm).locale('id').format('MMMM YYYY'));
                                }
                                else {
                                    arrayTxt.push(moment(sortedArray[i].nm).locale('id').format('DD MMMM YYYY'));
                                }
                                arrayNominal.push(parseFloat(sortedArray[i].nominal));
                                omzet = parseFloat(omzet) + parseFloat(sortedArray[i].nominal);
                            }

                            $("#txtOmzet").html(parseFloat(omzet).toLocaleString('id-ID', { maximumFractionDigits: 0}));
                            var element = document.getElementById("kt_stats_widget_11_chart_custom_verstand");

                            var height = parseInt(KTUtil.css(element, 'height'));
                            var color = KTUtil.hasAttr(element, 'data-color') ? KTUtil.attr(element, 'data-color') : 'success';

                            if (!element) {
                                return;
                            }

                            var options = {
                                series: [{
                                    name: 'Penjualan '+jenisPeriode,
                                    data: arrayNominal
                                }],
                                chart: {
                                    type: 'area',
                                    height: 150,
                                    toolbar: {
                                        show: false
                                    },
                                    zoom: {
                                        enabled: false
                                    },
                                    sparkline: {
                                        enabled: true
                                    }
                                },
                                plotOptions: {},
                                legend: {
                                    show: false
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                fill: {
                                    type: 'solid',
                                    opacity: 1
                                },
                                stroke: {
                                    curve: 'smooth',
                                    show: true,
                                    width: 3,
                                    colors: [KTApp.getSettings()['colors']['theme']['base'][color]]
                                },
                                xaxis: {
                                    categories: arrayTxt,
                                    // axisBorder: {
                                    //     show: false,
                                    // },
                                    // axisTicks: {
                                    //     show: false
                                    // },
                                    // labels: {
                                    //     show: false,
                                    //     style: {
                                    //         colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                                    //         fontSize: '12px',
                                    //         fontFamily: KTApp.getSettings()['font-family']
                                    //     }
                                    // },
                                    // crosshairs: {
                                    //     show: false,
                                    //     position: 'front',
                                    //     stroke: {
                                    //         color: KTApp.getSettings()['colors']['gray']['gray-300'],
                                    //         width: 1,
                                    //         dashArray: 3
                                    //     }
                                    // },
                                    // tooltip: {
                                    //     enabled: true,
                                    //     formatter: undefined,
                                    //     offsetY: 0,
                                    //     style: {
                                    //         fontSize: '12px',
                                    //         fontFamily: KTApp.getSettings()['font-family']
                                    //     }
                                    // }
                                },
                                // yaxis: {
                                //     min: 0,
                                //     max: 55,
                                //     labels: {
                                //         show: false,
                                //         style: {
                                //             colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                                //             fontSize: '12px',
                                //             fontFamily: KTApp.getSettings()['font-family']
                                //         }
                                //     }
                                // },
                                states: {
                                    normal: {
                                        filter: {
                                            type: 'none',
                                            value: 0
                                        }
                                    },
                                    hover: {
                                        filter: {
                                            type: 'none',
                                            value: 0
                                        }
                                    },
                                    active: {
                                        allowMultipleDataPointsSelection: false,
                                        filter: {
                                            type: 'none',
                                            value: 0
                                        }
                                    }
                                },
                                tooltip: {
                                    style: {
                                        fontSize: '12px',
                                        fontFamily: KTApp.getSettings()['font-family']
                                    },
                                    y: {
                                        formatter: function (val) {
                                            return "Rp " + parseFloat(val).toLocaleString('id-ID', { maximumFractionDigits: 0})
                                        }
                                    }
                                },
                                colors: [KTApp.getSettings()['colors']['theme']['light'][color]],
                                markers: {
                                    colors: [KTApp.getSettings()['colors']['theme']['light'][color]],
                                    strokeColor: [KTApp.getSettings()['colors']['theme']['base'][color]],
                                    strokeWidth: 3
                                }
                            };

                            var chart = new ApexCharts(element, options);
                            chart.render();
                            chart.updateOptions({
                                series: [{
                                    name: 'Penjualan '+jenisPeriode,
                                    data: arrayNominal
                                }],
                                xaxis: {
                                    categories: arrayTxt,
                                },
                            });
                        }
                    }
                });
            }

            function getProfitData(jenisPeriode) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/chartProfit",
                    method: 'POST',
                    data: {
                        jenis_periode: jenisPeriode
                    },
                    success: function(result){
                        if (result.length > 0) {
                            var arrayTxt = [];
                            var arrayNominal =[];
                            var profit = 0;

                            var sortedArray = result.sort(function(a,b) {
                                return new Date(a.nm) - new Date(b.nm)
                            });

                            for (var i = 0; i < sortedArray.length; i++) {
                                if (jenisPeriode == "Bulanan") {
                                    arrayTxt.push(moment(sortedArray[i].nm).locale('id').format('MMMM YYYY'));
                                }
                                else {
                                    arrayTxt.push(moment(sortedArray[i].nm).locale('id').format('DD MMMM YYYY'));
                                }
                                arrayNominal.push(parseFloat(sortedArray[i].nominal));
                                profit = parseFloat(profit) + parseFloat(sortedArray[i].nominal);
                            }

                            $("#txtProfit").html(parseFloat(profit).toLocaleString('id-ID', { maximumFractionDigits: 0}));
                            var element = document.getElementById("kt_stats_widget_12_chart_custom_verstand");

                            var height = parseInt(KTUtil.css(element, 'height'));
                            var color = KTUtil.hasAttr(element, 'data-color') ? KTUtil.attr(element, 'data-color') : 'primary';

                            if (!element) {
                                return;
                            }

                            var options = {
                                series: [{
                                    name: 'Profit '+jenisPeriode,
                                    data: arrayNominal
                                }],
                                chart: {
                                    type: 'area',
                                    height: height,
                                    toolbar: {
                                        show: false
                                    },
                                    zoom: {
                                        enabled: false
                                    },
                                    sparkline: {
                                        enabled: true
                                    }
                                },
                                plotOptions: {},
                                legend: {
                                    show: false
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                fill: {
                                    type: 'solid',
                                    opacity: 1
                                },
                                stroke: {
                                    curve: 'smooth',
                                    show: true,
                                    width: 3,
                                    colors: [KTApp.getSettings()['colors']['theme']['base'][color]]
                                },
                                xaxis: {
                                    categories: arrayTxt,
                                //     axisBorder: {
                                //         show: false,
                                //     },
                                //     axisTicks: {
                                //         show: false
                                //     },
                                //     labels: {
                                //         show: false,
                                //         style: {
                                //             colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                                //             fontSize: '12px',
                                //             fontFamily: KTApp.getSettings()['font-family']
                                //         }
                                //     },
                                //     crosshairs: {
                                //         show: false,
                                //         position: 'front',
                                //         stroke: {
                                //             color: KTApp.getSettings()['colors']['gray']['gray-300'],
                                //             width: 1,
                                //             dashArray: 3
                                //         }
                                //     },
                                //     tooltip: {
                                //         enabled: true,
                                //         formatter: undefined,
                                //         offsetY: 0,
                                //         style: {
                                //             fontSize: '12px',
                                //             fontFamily: KTApp.getSettings()['font-family']
                                //         }
                                //     }
                                },
                                // yaxis: {
                                //     min: 0,
                                //     max: 55,
                                //     labels: {
                                //         show: false,
                                //         style: {
                                //             colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                                //             fontSize: '12px',
                                //             fontFamily: KTApp.getSettings()['font-family']
                                //         }
                                //     }
                                // },
                                states: {
                                    normal: {
                                        filter: {
                                            type: 'none',
                                            value: 0
                                        }
                                    },
                                    hover: {
                                        filter: {
                                            type: 'none',
                                            value: 0
                                        }
                                    },
                                    active: {
                                        allowMultipleDataPointsSelection: false,
                                        filter: {
                                            type: 'none',
                                            value: 0
                                        }
                                    }
                                },
                                tooltip: {
                                    style: {
                                        fontSize: '12px',
                                        fontFamily: KTApp.getSettings()['font-family']
                                    },
                                    y: {
                                        formatter: function (val) {
                                            return "Rp " + parseFloat(val).toLocaleString('id-ID', { maximumFractionDigits: 0})
                                        }
                                    }
                                },
                                colors: [KTApp.getSettings()['colors']['theme']['light'][color]],
                                markers: {
                                    colors: [KTApp.getSettings()['colors']['theme']['light'][color]],
                                    strokeColor: [KTApp.getSettings()['colors']['theme']['base'][color]],
                                    strokeWidth: 3
                                }
                            };

                            var chart = new ApexCharts(element, options);
                            chart.render();
                            chart.updateOptions({
                                series: [{
                                    name: 'Profit '+jenisPeriode,
                                    data: arrayNominal
                                }],
                                xaxis: {
                                    categories: arrayTxt,
                                },
                            });
                        }
                    }
                });
            }

            $(document).ready(function() {
                var picker = $('#dashboard_daterangepicker');
                var start = moment();
                var end = moment();

                function cb(start, end, label) {
                    var title = '';
                    var range = '';

                    if ((end - start) < 100 || label == 'Hari Ini ') {
                        title = 'Hari Ini :';
                        range = start.locale('id').format('MMM D');
                    } else if (label == 'Kemarin') {
                        title = 'Kemarin:';
                        range = start.locale('id').format('MMM D');
                    } else {
                        range = start.locale('id').format('MMM D') + ' - ' + end.locale('id').format('MMM D');
                    }

                    $('#dashboard_daterangepicker_date').html(range);
                    $('#dashboard_daterangepicker_title').html(title);

                    //refresh dashboard
                    var url = window.location.href;
                    if (url.includes("dashboard")) {
                        refreshStatusPenjualan(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
                        refreshDataTagihan(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
                        refreshDataDashboard();
                        $("#startDate").val(start.format('YYYY-MM-DD'));
                        $("#endDate").val(end.format('YYYY-MM-DD'));
                    }
                }

                picker.daterangepicker({
                    direction: KTUtil.isRTL(),
                    startDate: start,
                    endDate: end,
                    opens: 'left',
                    applyClass: 'btn-primary',
                    cancelClass: 'btn-light-primary',
                    ranges: {
                        'Hari Ini': [moment(), moment()],
                        'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        '7 Hari Lalu': [moment().subtract(6, 'days'), moment()],
                        '30 Hari Lalu': [moment().subtract(29, 'days'), moment()],
                        'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                        'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);

                cb(start, end, '');
            });

            function refreshStatusPenjualan(start, end) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/dataInvSaleRefresh",
                    method: 'POST',
                    data: {
                        periode_awal:start,
                        periode_akhir:end
                    },
                    success: function(result){
                        if (result.length > 0) {
                            $("#ttlTagihanPenjualan").html(parseFloat(result[0].nominal).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#ttlFakturPenjualn").html(parseFloat(result[0].jml).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }
                        else {
                            $("#ttlTagihanPenjualan").html(0);
                            $("#ttlFakturPenjualn").html(0);
                        }
                    }
                });
            }

            function refreshDataTagihan(start, end) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/dataSales",
                    method: 'POST',
                    data: {
                        periode_awal:start,
                        periode_akhir:end
                    },
                    success: function(result){
                        if (result != "") {
                            //Progress Bar
                            var totalTagihan = parseFloat(result.nominalInvPaid) + parseFloat(result.nominalInvNotPaid);
                            var totalTagihanJT = parseFloat(result.nominalInvDue) + parseFloat(result.nominalInvNotDue);

                            var persenTagihanLunas = parseFloat(result.nominalInvPaid) / parseFloat(totalTagihan) * 100;
                            var persenTagihanBelumLunas = parseFloat(result.nominalInvNotPaid) / parseFloat(totalTagihan) * 100;

                            var persenTagihanJT = parseFloat(result.nominalInvDue) / parseFloat(totalTagihanJT) * 100;
                            var persenTagihanBelumJT = parseFloat(result.nominalInvNotDue) / parseFloat(totalTagihanJT) * 100;

                            if (parseFloat(result.nominalInvPaid) == 0) {
                                $('div#persentaseTagihanLunas').width(0 + '%');
                                $("#persentaseTagihan1").html(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2})+ '%');
                            }
                            else {
                                $('div#persentaseTagihanLunas').width(persenTagihanLunas + '%');
                                $("#persentaseTagihan1").html(parseFloat(persenTagihanLunas).toLocaleString('id-ID', { maximumFractionDigits: 2})+ '%');
                            }

                            if (parseFloat(result.nominalInvNotPaid) == 0) {
                                $('div#persentaseTagihanBelumLunas').width(0 + '%');
                                $("#persentaseTagihan2").html(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2})+ '%');
                            }
                            else {
                                $('div#persentaseTagihanBelumLunas').width(persenTagihanBelumLunas + '%');
                                $("#persentaseTagihan2").html(parseFloat(persenTagihanBelumLunas).toLocaleString('id-ID', { maximumFractionDigits: 2})+ '%');
                            }

                            if (parseFloat(result.nominalInvDue) == 0) {
                                $('div#persentaseTagihanJT').width(0 + '%');
                                $("#persentaseTagihan3").html(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2})+ '%');
                            }
                            else {
                                $('div#persentaseTagihanJT').width(persenTagihanJT + '%');
                                $("#persentaseTagihan3").html(parseFloat(persenTagihanJT).toLocaleString('id-ID', { maximumFractionDigits: 2})+ '%');
                            }

                            if (parseFloat(result.nominalInvNotDue) == 0) {
                                $('div#persentaseTagihanBelumJT').width(0 + '%');
                                $("#persentaseTagihan4").html(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2})+ '%');
                            }
                            else {
                                $('div#persentaseTagihanBelumJT').width(persenTagihanBelumJT + '%');
                                $("#persentaseTagihan4").html(parseFloat(persenTagihanBelumJT).toLocaleString('id-ID', { maximumFractionDigits: 2})+ '%');
                            }

                            $("#ttlTagihanLunas").html(parseFloat(result.nominalInvPaid).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#jmlTagihanLunas").html(parseFloat(result.ttlInvPaid).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#ttlTagihanBelumLunas").html(parseFloat(result.nominalInvNotPaid).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#jmlTagihanBelumLunas").html(parseFloat(result.ttlInvNotPaid).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#ttlTagihanJT").html(parseFloat(result.nominalInvDue).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#jmlTagihanJT").html(parseFloat(result.ttlInvDue).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#ttlTagihanBelumJT").html(parseFloat(result.nominalInvNotDue).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#jmlTagihanBelumJT").html(parseFloat(result.ttlInvNotDue).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }
                        else {
                            $("#ttlTagihanLunas").html(0);
                            $("#jmlTagihanLunas").html(0);
                            $("#ttlTagihanBelumLunas").html(0);
                            $("#jmlTagihanBelumLunas").html(0);
                            $("#ttlTagihanJT").html(0);
                            $("#jmlTagihanJT").html(0);
                            $("#ttlTagihanBelumJT").html(0);
                            $("#jmlTagihanBelumJT").html(0);
                        }
                    }
                });
            }



            function getDataTagihan(mode) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/DataSalesDetail",
                    method: 'POST',
                    data: {
                        periode_awal: $("#startDate").val(),
                        periode_akhir: $("#endDate").val(),
                        mode: mode,
                    },
                    success: function(result){
                        if (result != "") {
                            $('#list_invoice').DataTable().destroy();
                            $('#list_invoice tbody').empty();
                            $("#list_invoice").append(result.txtInv);

                            var table = $('#list_invoice').DataTable({
                                "columnDefs": [
                                    { "visible": false, "targets": 1 }
                                ],
                                "order": [[ 1, 'asc' ]],
                                "displayLength": 25,
                                "destroy": true,
                                "rowGroup" : {

                                    startRender: function ( rows, group ) {
                                        return $('<tr/>')
                                            .append( '<td colspan="4">'+group+'</td>' ) ;

                                    },

                                    endRender: function ( rows, group ) {
                                        var totalTagihan = rows
                                            .data()
                                            .pluck(4)
                                            .reduce( function (a, b) {
                                                return a + b.replace(/[^\d]/g, '')*1;
                                            }, 0);
                                        totalTagihan = $.fn.dataTable.render.number('.', ',', 0).display( totalTagihan );

                                        return $('<tr/>')
                                            .append( '<td colspan="3" style="text-align: left;border-bottom: 1px solid black;">Total Tagihan : </td>' )
                                            .append( '<td style="text-align: right;border-bottom: 1px solid black;">'+totalTagihan+'</td>' );
                                    },

                                    dataSrc: 1
                                }
                            });

                            $('#modal_list_invoice').modal('toggle');
                        }
                    }
                });
            }

            $('#modal_list_invoice').on('hidden.bs.modal', function () {
                $(".modal-backdrop").remove();
            })

            function refreshDataDashboard() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/GetDashboardData",
                    method: 'POST',
                    data: {

                    },
                    success: function(result){
                        if (result != null) {
                            // $("#jmlInvPPN").html("Jumlah Invoice Belum Digenerate : " + result.jmlInvPPn);
                            $("#rankItems").append(result.txtRank);
                            $("#rankCustomer").append(result.txtRankCust);
                            var piutang = result.piutang ?? 0;
                            var hutang = result.hutang ?? 0;
                            $("#ttlPiutangDashboard").text(parseFloat(piutang).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#ttlHutangDashBoard").text(parseFloat(hutang).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }
                        else {
                            // $("#jmlInvPPN").html("Jumlah Invoice Belum Digenerate : 0");
                            $("#ttlPiutangDashboard").text("0.00");
                            $("#ttlHutangDashBoard").text("0.00");
                        }
                    }
                });
            }


            $(".btnChartOmzet").on("click", function() {
                var periode = $(this).html();
                getOmzetData(periode);
            });

            $(".btnChartProfit").on("click", function() {
                var periode = $(this).html();
                getProfitData(periode);
            });

            //Tabel Seri Faktur Pajak
            // $(document).ready(function() {

            //     var datatable = $('#list_serial').KTDatatable({
            //         data: {
            //             type: 'remote',
            //             source: {
            //                 read: {
            //                     url: '/GetTaxSerialNumber',
            //                     method: 'POST',
            //                     headers : {
            //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            //                     },
            //                 }
            //             },
            //             pageSize: 100,
            //             serverPaging: true,
            //             serverFiltering: false,
            //             serverSorting: true,
            //             saveState: false
            //         },

            //         layout: {
            //             scroll: true,
            //             height: 'auto',
            //             footer: false
            //         },

            //         sortable: true,

            //         filterable: true,

            //         pagination: true,

            //         search: {
            //             input: $('#list_serial_search_query')
            //         },

            //         rows: {
            //             autoHide:false
            //         },

            //         columns: [
            //             {
            //                 field: 'id',
            //                 title: '#',
            //                 sortable: false,
            //                 width: 0,
            //                 type: 'number',
            //                 selector: false,
            //                 textAlign: 'center',
            //                 visible:false,
            //             },
            //             {
            //                 field: 'tahun_berlaku_seri',
            //                 width: 70,
            //                 title: 'Tahun Pajak',
            //                 textAlign: 'center',
            //                 autoHide: false,
            //                 template: function(row) {
            //                     var txt = "";
            //                     txt += '<span class="font-weight-bold">'+row.tahun_berlaku_seri+'</span>';
            //                     return txt;
            //                 },
            //             },
            //             {
            //                 field: 'nomor_seri_dari',
            //                 width: 120,
            //                 title: 'Dari',
            //                 textAlign: 'center',
            //                 autoHide: false,
            //                 template: function(row) {
            //                     var txt = "";
            //                     txt += '<span class="font-weight-bold">'+row.nomor_seri_dari+'</span>';
            //                     return txt;
            //                 },
            //             },
            //             {
            //                 field: 'nomor_seri_sampai',
            //                 width: 120,
            //                 title: 'Sampai',
            //                 textAlign: 'center',
            //                 autoHide: false,
            //                 template: function(row) {
            //                     var txt = "";
            //                     txt += '<span class="font-weight-bold">'+row.nomor_seri_sampai+'</span>';
            //                     return txt;
            //                 },
            //             },
            //             {
            //                 field: 'jumlah_no_seri',
            //                 width: 70,
            //                 title: 'Jumlah Faktur Pajak',
            //                 textAlign: 'left',
            //                 autoHide: false,
            //                 template: function(row) {
            //                     var txt = "";
            //                     txt += '<span class="font-weight-bold">'+parseFloat(row.jumlah_no_seri).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span>';
            //                     return txt;
            //                 },
            //             },
            //             {
            //                 field: 'sisa_jumlah',
            //                 width: 70,
            //                 title: 'Jumlah Sisa',
            //                 textAlign: 'left',
            //                 autoHide: false,
            //                 template: function(row) {
            //                     var txt = "";
            //                     txt += '<span class="font-weight-bold">'+parseFloat(row.sisa_jumlah).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span>';
            //                     return txt;
            //                 },
            //             },
            //         ],
            //     });
            // });

            $(document).ready(function() {

                var datatable = $('#list_monitor').KTDatatable({
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                url: '/GetStockMonitor',
                                method: 'POST',
                                headers : {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                },
                            }
                        },
                        pageSize: 100,
                        serverPaging: false,
                        serverFiltering: false,
                        serverSorting: false,
                        saveState: false
                    },

                    layout: {
                        scroll: true,
                        height: 'auto',
                        footer: false
                    },

                    sortable: true,

                    filterable: true,

                    pagination: true,

                    search: {
                        input: $('#list_monitor_search_query')
                    },

                    rows: {
                        autoHide:false
                    },

                    columns: [
                        {
                            field: 'id',
                            title: '#',
                            sortable: false,
                            width: 0,
                            type: 'number',
                            selector: false,
                            textAlign: 'center',
                            visible:false,
                            autoHide: false,
                        },
                        {
                            field: 'nama_item',
                            title: 'Nama Barang',
                            width: 350,
                            textAlign: 'left',
                            autoHide: false,
                            template: function(row) {
                                var txt = "";
                                    if (row.stok_item != null) {
                                        txt = "<a href='#' class='text-secondary text-hover-primary' data-toggle='modal' data-target='#modal_detail_lokasi' title='Detail Lokasi' onclick='viewDetailLokasi(" + row.id + ", " + row.id_satuan + ");return false;'>";
                                        txt += '<span class="font-weight-bold">'+row.nama_item+'</span>';
                                        txt += "</a>";
                                    }
                                    else {
                                        txt += '<span class="font-weight-bold">'+row.nama_item+'</span>';
                                    }
                                    if(row.value_spesifikasi != null) {
                                        txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">' +'('+row.value_spesifikasi+')'+row.kode_item.toUpperCase()+ '</span>';
                                    }
                                    else {
                                        txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">' +row.kode_item.toUpperCase()+ '</span>';
                                    }
                                    txt += '<span class="label label-md label-outline-primary label-inline mt-1 mr-1">' + row.nama_merk.toUpperCase() + '</span>';
                                    txt += '<span class="label label-md label-outline-primary label-inline mt-1">' + row.nama_kategori + '</span>';
                                    return txt;
                            }
                        },

                        {
                            field: 'stok_item',
                            width: 'auto',
                            title: 'Jumlah Stok',
                            type: 'number',
                            textAlign: 'center',
                            autoHide: false,
                            template: function(row) {
                                return parseFloat(row.stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            },
                        },
                        {
                            field: 'nama_satuan',
                            title: 'Satuan',
                            width: 'auto',
                            autoHide: false,
                            textAlign: 'center',
                            template: function(row) {
                                if (row.nama_satuan != null) {
                                    return row.nama_satuan.toUpperCase();
                                }
                                else {
                                    return '-';
                                }
                            },
                        },
                        {
                            field: 'Status',
                            title: 'Status Stok',
                            width: 'auto',
                            textAlign: 'center',
                            autoHide: false,
                            template: function(row) {
                                if (parseFloat(row.stok_item) < 0) {
                                    return '<span class="label label-rounded font-weight-bold label-lg label-light-danger label-inline">Stok Minus</span>';
                                }
                                else if(parseFloat(row.stok_item) == 0) {
                                    return '<span class="label label-rounded font-weight-bold label-lg label-light-default label-inline">Kosong</span>';
                                }
                                else if (parseFloat(row.stok_item) <= parseFloat(row.stok_minimum)) {
                                    return '<span class="label label-rounded font-weight-bold label-lg label-light-warning label-inline">Stok Menipis</span>';
                                }
                                else if (parseFloat(row.stok_item) > parseFloat(row.stok_maksimum)) {
                                    return '<span class="label label-rounded font-weight-bold label-lg label-light-danger label-inline">Stok Melebihi Batas</span>';
                                }
                                else {
                                    return '<span class="label label-rounded font-weight-bold label-lg label-light-primary label-inline">Normal</span>';
                                }
                            },
                        },
                        {
                            field: 'txtKode',
                            title: 'Txt Barang',
                            autoHide: true,
                            textAlign: 'center',
                            width: 50,
                            visible:false,
                            template: function(row) {
                                if(row.value_spesifikasi != null) {
                                    return '('+row.value_spesifikasi+')'+row.kode_item.toUpperCase() + "<span id='txt_"+row.id+"'>("+row.value_spesifikasi+')'+row.kode_item.toUpperCase() + " - " +row.nama_item.toUpperCase()+"</span>";

                                }
                                else {
                                    return row.kode_item.toUpperCase() + "<span id='txt_"+row.id+"'>"+row.kode_item.toUpperCase() + " - " +row.nama_item.toUpperCase()+"</span>";
                                }
                            },
                        },
                    ],
                });
            });

            $(document).ready(function () {
                $("#table_stock_search_index").on('change', function(e) {
                    var datatable = $('#table_stock').KTDatatable();
                        datatable.setDataSourceParam('idIndex', $("#table_stock_search_index").val());
                        datatable.reload();
                });
            });

            $(document).ready(function () {
                $('#list_monitor_search_supplier').select2({
                    allowClear: true
                });

                $('#list_monitor_search_index').select2({
                    allowClear: true
                });

                $('#list_monitor_search_status').select2({
                    allowClear: true
                });
            });

            $(document).ready(function() {

                var datatable = $('#list_item_lokasi').KTDatatable({
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                url: '/Stock/GetDataPerIndex',
                                method: 'POST',
                                headers : {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                },

                            }
                        },
                        pageSize: 100,
                        serverPaging: false,
                        serverFiltering: false,
                        serverSorting: false,
                        saveState: false
                    },

                    layout: {
                        scroll: false,
                        height: 'auto',
                        footer: false
                    },

                    sortable: true,

                    filterable: true,

                    pagination: false,

                    search: {
                        input: $('#table_lokasi_search_query')
                    },

                    rows: {
                        autoHide: false
                    },

                    columns: [
                        {
                            field: 'id',
                            title: '#',
                            sortable: false,
                            width: 20,
                            type: 'number',
                            selector: false,
                            textAlign: 'center',
                            visible:false,
                        },
                        {
                            field: 'jenis_sumber',
                            width: 'auto',
                            title: 'Sumber',
                            textAlign: 'left',
                            template: function(row) {
                                var statusTxt = "";

                                if (row.jenis_sumber == "1") {
                                    statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">Penerimaan</span>';
                                }
                                else if (row.jenis_sumber == "2") {
                                    statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">Produksi</span>';
                                }
                                else if (row.jenis_sumber == "3") {
                                    statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Transfer Stok</span>';
                                }
                                else if (row.jenis_sumber == "4") {
                                    statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Konversi Stok</span>';
                                }
                                else if (row.jenis_sumber == "5") {
                                    statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Adjustment</span>';
                                }
                                else if (row.jenis_sumber == "6") {
                                    statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">Retur</span>';
                                }

                                return statusTxt;
                            },
                            autoHide: false,
                        },
                        {
                            field: 'id_index',
                            title: 'Lokasi',
                            width: 'auto',
                            textAlign: 'center',
                            autoHide: false,
                            template: function(row) {
                                if (row.txt_index != null) {
                                    return row.txt_index.toUpperCase();
                                }
                                else {
                                    return '-';
                                }
                            },
                        },
                        {
                            field: 'stok_item',
                            width: 'auto',
                            title: 'Qty',
                            textAlign: 'center',
                            autoHide: true,
                            template: function(row) {
                                if (row.stok_item != null) {
                                    return parseFloat(row.stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                                }
                                else {
                                    return '0';
                                }
                            },
                        },
                    ],
                });
            });

            function viewDetailLokasi(id, idSatuan) {

                var datatable = $('#list_item_lokasi').KTDatatable();
                    datatable.setDataSourceParam('idProduct', id);
                    datatable.setDataSourceParam('idSatuan', idSatuan);
                    datatable.reload();

                $("#txtNamaLokasi").text($("#txt_"+id).text().toUpperCase());

            }

        </script>

        {{-- Includable JS --}}
        @yield('scripts')

    </body>
</html>

