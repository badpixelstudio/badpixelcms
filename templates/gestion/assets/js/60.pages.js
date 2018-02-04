/**** INDEX.JS ****/
var Index = function () {

    return {
        //main function
        init: function () {
            Metronic.addResizeHandler(function () {
                jQuery('.vmaps').each(function () {
                    var map = jQuery(this);
                    map.width(map.parent().width());
                });
            });
        },

        initCalendar: function () {
            if (!jQuery().fullCalendar) {
                return;
            }

            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();

            var h = {};

            if ($('#calendar').width() <= 400) {
                $('#calendar').addClass("mobile");
                h = {
                    left: 'title, prev, next',
                    center: '',
                    right: 'today,month,agendaWeek,agendaDay'
                };
            } else {
                $('#calendar').removeClass("mobile");
                if (Metronic.isRTL()) {
                    h = {
                        right: 'title',
                        center: '',
                        left: 'prev,next,today,month,agendaWeek,agendaDay'
                    };
                } else {
                    h = {
                        left: 'title',
                        center: '',
                        right: 'prev,next,today,month,agendaWeek,agendaDay'
                    };
                }
            }

            $('#calendar').fullCalendar('destroy'); // destroy the calendar
            $('#calendar').fullCalendar({ //re-initialize the calendar
                disableDragging: false,
                header: h,
                editable: true,
                lang: 'es',
                events: "calendar/home"
            });
        },

    };
}();

/**** TASKS.JS ****/
var Tasks = function () {
    return {
        //main function to initiate the module
        initDashboardWidget: function () {
            $('.task-list input[type="checkbox"]').change(function() {
                if ($(this).is(':checked')) { 
                    $(this).parents('li').addClass("task-done"); 
                } else { 
                    $(this).parents('li').removeClass("task-done"); 
                }
            }); 
        }

    };
}();

/**** TABLE-ADVANCED.JS ****/
var TableAdvanced = function () {
    var initTable1 = function () {
        var table = $('#sample_1');
        /* Table tools samples: https://www.datatables.net/release-datatables/extras/TableTools/ */
        var oTable = table.dataTable({
            "aaSorting": [
                [0, 'asc']
            ],
            "aLengthMenu": [[10,25,50,100, 250, 500, -1], [10,25,50,100, 250, 500, "Todos"]],
            "iDisplayLength": 25
        });
        var tableWrapper = $('#sample_1_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper
        jQuery('.dataTables_filter input', tableWrapper).addClass("form-control input-small input-inline"); // modify table search input
        jQuery('.dataTables_length select', tableWrapper).addClass("form-control input-small"); // modify table per page dropdown
        jQuery('.dataTables_length select', tableWrapper).select2(); // initialize select2 dropdown

        table.delegate('.delete-confirm','click',function(){
            var temp=$(this).parent("li").parent("ul").parent("li").parent("td").parent("tr");
            var myUrl=$(this).attr('href');
            bootbox.confirm("¿Borrar el elemento seleccionado?", function(result) {
                if (result) {
                    $.ajax({
                        type: 'GET',
                        url: myUrl,
                        data: '',
                        success:function(msj){  
                            //$.wl_Alert(msj,'note','#content');
                            if ( msj == 1 ){
                                temp.fadeOut(800);                     
                            }
                            else{
                                bootbox.alert("No se ha podido eliminar el elemento"); 
                            }
                        },
                        error:function(){
                            bootbox.alert("Error interno"); 
                        }
                    });
                }
            }); 
            return false;
        });
        table.delegate('.delete-confirm-row','click',function(){
            var temp=$(this).closest('tr').prev('tr');
            var myUrl=$(this).attr('href');
            bootbox.confirm("¿Borrar el elemento seleccionado?", function(result) {
                if (result) {
                    $.ajax({
                        type: 'GET',
                        url: myUrl,
                        data: '',
                        success:function(msj){  
                            //$.wl_Alert(msj,'note','#content');
                            if ( msj == 1 ){
                                temp.fadeOut(800); 
                                $('.open').removeClass('open');
                                $('.children-remove').remove();                   
                            }
                            else{
                                bootbox.alert("No se ha podido eliminar el elemento"); 
                            }
                        },
                        error:function(){
                            bootbox.alert("Error interno"); 
                        }
                    });
                }
            }); 
            return false;
        });
    }

    var initTable2 = function () {
        var table = $('#sample_2');
        
        table.delegate('.delete-confirm','click',function(){
            var temp=$(this).parent("li").parent("ul").parent("li").parent("td").parent("tr");
            var myUrl=$(this).attr('href');
            bootbox.confirm("¿Borrar el elemento seleccionado?", function(result) {
                if (result) {
                    $.ajax({
                        type: 'GET',
                        url: myUrl,
                        data: '',
                        success:function(msj){  
                            //$.wl_Alert(msj,'note','#content');
                            if ( msj == 1 ){
                                temp.fadeOut(800);                     
                            }
                            else{
                                bootbox.alert("No se ha podido eliminar el elemento"); 
                            }
                        },
                        error:function(){
                            bootbox.alert("Error interno"); 
                        }
                    });
                }
            }); 
            return false;
        });
        table.delegate('.delete-confirm-row','click',function(){
            var temp=$(this).closest('tr').prev('tr');
            var myUrl=$(this).attr('href');
            bootbox.confirm("¿Borrar el elemento seleccionado?", function(result) {
                if (result) {
                    $.ajax({
                        type: 'GET',
                        url: myUrl,
                        data: '',
                        success:function(msj){  
                            //$.wl_Alert(msj,'note','#content');
                            if ( msj == 1 ){
                                temp.fadeOut(800); 
                                $('.open').removeClass('open');
                                $('.children-remove').remove();                   
                            }
                            else{
                                bootbox.alert("No se ha podido eliminar el elemento"); 
                            }
                        },
                        error:function(){
                            bootbox.alert("Error interno"); 
                        }
                    });
                }
            }); 
            return false;
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().dataTable) {
                return;
            }
            initTable1();
            initTable2();
        }
    };
}();

/**** UI-NESTABLE.JS ****/
var UINestable = function () {
    var updateOutputOrder = function (e) {
        var href=$(e.target).attr("script");
        var list = e.length ? e : $(e.target),
            output = list.data('output');
        if (window.JSON) {
            $.ajax({
                type: 'POST',
                url: href,
                data: 'order=' + window.JSON.stringify(list.nestable('serialize')),
                success:function(msj){
                    //console.log('Order save sucessfully');
                },
                error:function(){
                    //console.log('Error save order');
                }
            }); 
        } else {
            output.val('JSON browser support required for this demo.');
        }
    };

    return {
        //main function to initiate the module
        init: function () {
            $('.nestable').each(function () {
                if ($(this).attr('maxDepth')) {
                    var val=$(this).attr('maxDepth');                   
                    $(this).nestable({ maxDepth: val}).on('change', updateOutputOrder);
                } else {
                    $(this).nestable().on('change', updateOutputOrder);
                }
            })
            //$('.nestable').nestable({maxDepth: 1}).on('change', updateOutputOrder);
            //updateOutput($('#cats_tree').data('output', $('#nestable_list_2_output')));
        }
    };
}();

/**** UI-ALERT-DIALOG-API.JS ****/
var UIAlertDialogApi = function () {
    var handleDialogs = function() {
        $('#demo_1').click(function(){
                bootbox.alert("Hello world!");    
            });
            //end #demo_1
            $('#demo_2').click(function(){
                bootbox.alert("Hello world!", function() {
                    alert("Hello world callback");
                });  
            });
            //end #demo_2
            $('#demo_3').click(function(){
                bootbox.confirm("Are you sure?", function(result) {
                   alert("Confirm result: "+result);
                }); 
            });
            //end #demo_3
            $('#demo_4').click(function(){
                bootbox.prompt("What is your name?", function(result) {
                    if (result === null) {
                        alert("Prompt dismissed");
                    } else {
                        alert("Hi <b>"+result+"</b>");
                    }
                });
            });
            //end #demo_6
            $('#demo_5').click(function(){
                bootbox.dialog({
                    message: "I am a custom dialog",
                    title: "Custom title",
                    buttons: {
                      success: {
                        label: "Success!",
                        className: "green",
                        callback: function() {
                          alert("great success");
                        }
                      },
                      danger: {
                        label: "Danger!",
                        className: "red",
                        callback: function() {
                          alert("uh oh, look out!");
                        }
                      },
                      main: {
                        label: "Click ME!",
                        className: "blue",
                        callback: function() {
                          alert("Primary button");
                        }
                      }
                    }
                });
            });
            //end #demo_7
            $(document).delegate('.levels_name','click',function() {
                var _this=$(this);
                var _module=$(this).attr("href");
                bootbox.prompt("Definir el nombre del módulo '" + $(this).text() + "'", function(result) {
                    if ((result !== null) && (result != "")) {
                        $.ajax({
                        type: 'POST',
                        url: 'levels/changename/module/' + _module + '/name/' + result,
                        success:function(msj){  
                            _this.text(result);
                        },
                        error:function(){
                            console.log('error');
                        }
                    });
                    }
                });
                return false;
            });

    }

    var handleAlerts = function() {
        $('#alert_show').click(function(){
            Metronic.alert({
                container: $('#alert_container').val(), // alerts parent container(by default placed after the page breadcrumbs)
                place: $('#alert_place').val(), // append or prepent in container 
                type: $('#alert_type').val(),  // alert's type
                message: $('#alert_message').val(),  // alert's message
                close: $('#alert_close').is(":checked"), // make alert closable
                reset: $('#alert_reset').is(":checked"), // close all previouse alerts first
                focus: $('#alert_focus').is(":checked"), // auto scroll to the alert after shown
                closeInSeconds: $('#alert_close_in_seconds').val(), // auto close after defined seconds
                icon: $('#alert_icon').val() // put icon before the message
            });
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleDialogs();
            handleAlerts();
        }
    };
}();

/**** FORM-VALIDATION ****/
var FormValidation = function () {
    // basic validation
    var handleValidation1 = function() {
        // for more info visit the official plugin documentation: 
        // http://docs.jquery.com/Plugins/Validation
        var form1 = $('#form');
        var error1 = $('#form_error', form1);
        var success1 = $('#form_saving', form1);
        form1.validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block clearfix', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            errorPlacement: function(error, element) { 
                var _pos_err=element.parent('div');
                if (_pos_err.parent("div").hasClass("form-group")) { _pos_err=_pos_err.parent("div"); }
                error.appendTo(_pos_err); 
            },
            invalidHandler: function (event, validator) { //display error alert on form submit              
                success1.hide();
                error1.show();
                Metronic.scrollTo(error1, -200);
            },
            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label
                    .closest('.form-group').removeClass('has-error'); // set success class to the control group
            },
            submitHandler: function (form) {
                $('.date-picker').each( function(index) {
                    $(this).val($(this).val().split("/").reverse().join("-"));
                });
                $('.form_datetime').each( function(index) {
                    var partes=$(this).val().split(" ");
                    partes[0]=partes[0].split("/").reverse().join("-");
                    $(this).val(partes.join(" "));
                });
                $('.form-actions').hide();
                success1.show();
                error1.hide();
                ProcessUploadedFiles();
                form.submit();
            }
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            $('form').find('input[type=file]').wl_File();
            handleValidation1();
        }
    };
}();

/**** COMPONENTS-PICKERS.JS ****/
var ComponentsPickers = function () {
    ;(function($){
        $.fn.datepicker.dates['es'] = {
            days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
            daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"],
            daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
            today: "Hoy"
        };
    }(jQuery));
    ;(function($){
    $.fn.datetimepicker.dates['es'] = {
            days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
            daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"],
            daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
            today: "Hoy",
            suffix: [],
            meridiem: []
        };
    }(jQuery));
    var handleDatePickers = function () {
        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: Metronic.isRTL(),
                format: 'dd/mm/yyyy',
                language: 'es',
                weekStart: 1,
                firstDay: 1,
                autoclose: true
            });
            $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }
    }
    var handleTimePickers = function () {
        if (jQuery().timepicker) {
            $('.timepicker-default').timepicker({
                autoclose: true,
                language: 'es',
                weekStart: 1,
                showSeconds: true,
                minuteStep: 1
            });
            $('.timepicker-no-seconds').timepicker({
                autoclose: true,
                language: 'es',
                weekStart: 1,
                minuteStep: 5,
                showMeridian: false
            });
            $('.timepicker-24').timepicker({
                autoclose: true,
                language: 'es',
                weekStart: 1,
                minuteStep: 5,
                showSeconds: false,
                showMeridian: false
            });
            // handle input group button click
            $('.timepicker').parent('.input-group').on('click', '.input-group-btn', function(e){
                e.preventDefault();
                $(this).parent('.input-group').find('.timepicker').timepicker('showWidget');
            });
        }
    }

    var handleDateRangePickers = function () {
        if (!jQuery().daterangepicker) {
            return;
        }
        $('#defaultrange').daterangepicker({
                opens: (Metronic.isRTL() ? 'left' : 'right'),
                format: 'MM/DD/YYYY',
                separator: ' to ',
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/2014',
                language: 'es',
                weekStart: 1,
            },
            function (start, end) {
                console.log("Callback has been called!");
                $('#defaultrange input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );        
        $('#defaultrange_modal').daterangepicker({
                opens: (Metronic.isRTL() ? 'left' : 'right'),
                format: 'MM/DD/YYYY',
                separator: ' to ',
                language: 'es',
                weekStart: 1,
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/2014',
            },
            function (start, end) {
                $('#defaultrange_modal input').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );  
        // this is very important fix when daterangepicker is used in modal. in modal when daterange picker is opened and mouse clicked anywhere bootstrap modal removes the modal-open class from the body element.
        // so the below code will fix this issue.
        $('#defaultrange_modal').on('click', function(){
            if ($('#daterangepicker_modal').is(":visible") && $('body').hasClass("modal-open") == false) {
                $('body').addClass("modal-open");
            }
        });
        $('#reportrange').daterangepicker({
                opens: (Metronic.isRTL() ? 'left' : 'right'),
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                minDate: '01/01/2012',
                maxDate: '12/31/2014',
                dateLimit: {
                    days: 60
                },
                showDropdowns: true,
                showWeekNumbers: true,
                timePicker: false,
                timePickerIncrement: 1,
                timePicker12Hour: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                buttonClasses: ['btn'],
                applyClass: 'green',
                cancelClass: 'default',
                format: 'MM/DD/YYYY',
                separator: ' to ',
                locale: {
                    applyLabel: 'Metronicly',
                    fromLabel: 'From',
                    toLabel: 'To',
                    customRangeLabel: 'Custom Range',
                    daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                    monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    firstDay: 1
                }
            },
            function (start, end) {
                console.log("Callback has been called!");
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );
        //Set the initial state of the picker label
        $('#reportrange span').html(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
    }

    var handleDatetimePicker = function () {
        $(".form_datetime").datetimepicker({
            autoclose: true,
            isRTL: Metronic.isRTL(),
            format: "dd/mm/yyyy hh:ii",
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left")
        });
        $(".form_advance_datetime").datetimepicker({
            isRTL: Metronic.isRTL(),
            format: "dd MM yyyy - hh:ii",
            autoclose: true,
            todayBtn: true,
            startDate: "2013-02-14 10:00",
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
            minuteStep: 10
        });
        $(".form_meridian_datetime").datetimepicker({
            isRTL: Metronic.isRTL(),
            format: "dd MM yyyy - HH:ii P",
            showMeridian: true,
            autoclose: true,
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
            todayBtn: true
        });
        $('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
    }

    var handleClockfaceTimePickers = function () {
        if (!jQuery().clockface) {
            return;
        }
        $('.clockface_1').clockface();

        $('#clockface_2').clockface({
            format: 'HH:mm',
            trigger: 'manual'
        });
        $('#clockface_2_toggle').click(function (e) {
            e.stopPropagation();
            $('#clockface_2').clockface('toggle');
        });
        $('#clockface_2_modal').clockface({
            format: 'HH:mm',
            trigger: 'manual'
        });
        $('#clockface_2_modal_toggle').click(function (e) {
            e.stopPropagation();
            $('#clockface_2_modal').clockface('toggle');
        });
        $('.clockface_3').clockface({
            format: 'H:mm'
        }).clockface('show', '14:30');
    }

    var handleColorPicker = function () {
        if (!jQuery().colorpicker) {
            return;
        }
        $('.colorpicker-default').colorpicker({
            format: 'hex'
        });
        $('.colorpicker-rgba').colorpicker();
    }
   
    var handleSpinners = function () {
        $('.spinner').spinner({max:999999999});
    }

    var handleTagsInput = function () {
        if (!jQuery().tagsinput) {
            return;
        }
        //Tags Sencillas
        $('.tags').tagsinput();

        //Tags Fijas
        $('.tags-fixed').each(function (index) {

            var _this=$(this);
            var _suggestions=_this.attr("url-suggestions");
            var _prevalues=_this.attr('preload');
            console.log(_prevalues);
            var data = new Bloodhound({
              datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
              queryTokenizer: Bloodhound.tokenizers.whitespace,
              prefetch: { url: _suggestions, ttl: 0 },
            });
            data.clearRemoteCache();
            data.initialize();
            _this.tagsinput({
                typeaheadjs: {
                    name: 'data',
                    displayKey: 'text',
                    source: data.ttAdapter()
                },
                freeInput: false,
                itemValue: 'value',
                itemText: 'text',
                tagClass: function(item) {
                    switch (item.type) {
                      case 'user'   : return 'label label-primary';
                      case 'rol'  : return 'label label-danger label-important';
                      case 'other1': return 'label label-success';
                      case 'other2'   : return 'label label-default';
                      case 'other3'     : return 'label label-warning';
                    }
                  }
            });
            if (_prevalues!='""') { 
                valores=JSON.parse(_prevalues.replace("[","").replace("]",""));
                _this.tagsinput('add', valores);
                $("span[data-role*='remove']").hide();
                $(".twitter-typeahead").hide();
            }
        });
    }

    var handleInbox = function () {

        //Marcar mensajes de forma masiva...
        jQuery('body').on('change', '.mail-group-checkbox', function () {
            var set = jQuery('.mail-checkbox');
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                $(this).attr("checked", checked);
            });
            jQuery.uniform.update(set);
        });

        //Marcar como leido...
        $('#inbox-mark-as-read').click(function () {
            var myUrl=$(this).attr("href");
            $.ajax({
                type: 'POST',
                url: myUrl,
                data: $('.mail-checkbox:checked').serialize(),
                success:function(msj){  
                   location.reload();
                }
            });
            return false;
        });

        //Marcar como leido...
        $('#inbox-mark-as-unread').click(function () {
            var myUrl=$(this).attr("href");
            $.ajax({
                type: 'POST',
                url: myUrl,
                data: $('.mail-checkbox:checked').serialize(),
                success:function(msj){  
                   location.reload();
                }
            });
            return false;
        });

        //Borrar marcados...
        $('#inbox-mark-delete').click(function () {
            var myUrl=$(this).attr("href");
            $.ajax({
                type: 'POST',
                url: myUrl,
                data: $('.mail-checkbox:checked').serialize(),
                success:function(msj){  
                   location.reload();
                }
            });
            return false;
        });
    }

    var handleJSTree = function () {

        $('.jstree').each(function (index) {
            var myUrl=$(this).attr("data-url");
            // var data='';
            // $.ajax({
            //     type: 'POST',
            //     url: myUrl,
            //     success:function(msj){  
            //        data=msj;
            //     }
            // });

            $(this).jstree({
                'plugins': ["checkbox", "types", "ui", "massload"],
                'core': {
                    "themes" : {
                        "responsive": false
                    },    
                    'data' : {
                        "url" : myUrl,
                        "dataType" : "json" // needed only if you do not supply JSON headers
                    }
                },
                "types" : {
                    "default" : {
                        "icon" : "fa fa-folder icon-state-warning icon-lg"
                    },
                    "file" : {
                        "icon" : "fa fa-file icon-state-warning icon-lg"
                    }
                },
                "checkbox": { "three_state": false }
            });
        });

        $(".jstree").click(function () {
            var arr = $(this).jstree(true).get_selected();
            $("#"+$(this).attr("data-field")).val(arr.join(","));
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleDatePickers();
            handleTimePickers();
            handleDatetimePicker();
            handleDateRangePickers();
            handleClockfaceTimePickers();
            handleColorPicker();
            handleSpinners();
            handleTagsInput();
            handleInbox();
            handleJSTree();
        }
    };
}();

$(document).ready(function () {
    $(document).delegate('.dt-add-td','click',function () {
        var id=$(this).attr("id").replace("dt-itm-","");
        if ($(this).hasClass('open')) {
            $('.open').removeClass('open');
            $('#child-'+id).remove();
        } else {
            $('.children-remove').remove();
            $('.open').removeClass('open');
            var curRow=$(this).closest('tr');
            var newRow="<tr id='child-"+id+"' class='children-remove'><td colspan='7'><div class='pull-right'>" + $('.dt-opts-'+id).html() + "</div></td></tr>";
            $(this).addClass('open');
            curRow.after(newRow);
        }
    });
})