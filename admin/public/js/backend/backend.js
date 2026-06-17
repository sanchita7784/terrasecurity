/**
 * @author : Hardeep
 * this file only for backend
 */

$.blackdrop = {
    obj: null,
    events: [],
    init: function () {
        var _this = this;
        _this.obj = $("body").find(".black_drop_container:first");

        if (this.obj.length == 0) {
            var html = '<div class="black_drop_container" style="display:none;"></div>';
            $("body").prepend(html);
            _this.obj = $("body").find(".black_drop_container:first");
        }

        _this.obj.click(function () {
            _this.hide();
        });
    },
    onClick: function (fn) {
        if (typeof fn != "function") {
            console.error("blackDrop -> onClick() : argument should be function type");
        }

        this.obj.click(fn);
    },
    show: function () {
        this.obj.show();
    },
    hide: function () {
        this.obj.hide();
    }
};


$(document).ajaxError(function (event, xhr, settings, errorString) {
    if (xhr.status == 403) {
        $.events.onAjaxError(errorString, "Session is expired. Please Login");
    } else if (
        typeof xhr.responseText == "string" &&
        xhr.responseText.length > 0
    ) {
        $.events.onAjaxError(errorString, xhr.responseText, {
            url: settings.url,
        });
    }
});

function ajaxHandleResponse(url, response, callback) {

    var responseJson = {};
    if (typeof response == "object") {
        responseJson = response;
    } else {
        try {

            if (typeof (response) == "string") {
                response = response.trim();

                if (response.length == 0) {
                    $.events.onAjaxError("JSON Parse Error", "Empty Response", {
                        url: url,
                    });

                    return false;
                }

                var responseJson = JSON.parse(response);
            }
        } catch (e) {
            $.events.onAjaxError("JSON Parse Error", response, {
                url: url,
            });
            return false;
        }
    }

    if (typeof responseJson["status"] == "undefined") {
        $.events.onAjaxError("Missing", "Response JSON Should have status", {
            url: url,
        });
        return;
    }

    if (responseJson["status"] == "1" || responseJson["status"] == true) {
        if (typeof callback == "function") {
            callback(responseJson);
        }
    } else if (typeof responseJson["msg"] != "undefined") {
        $.events.onUserError(responseJson["msg"]);
    } else {
        $.events.onAjaxError("Missing", "Response JSON Should have msg", {
            url: url,
        });
    }
}


function confirmDialog(text, onYes) {
    Swal.fire({
        title: "Are you sure?",
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: constants.swal.button.confirm_color,
        cancelButtonColor: constants.swal.button.cancel_color,
    }).then(function (e) {
        if (e.value) {
            if (typeof onYes == "function") {
                onYes();
            }
        }
    });
}


function ajaxGetJson(url, callback) {
    $.loader.init();
    $.loader.setInfo("Loading...").show();

    $.get(url, function (response) {

        $.loader.hide();

        ajaxHandleResponse(url, response, callback);

    }).fail(function (xhr, status, title) {
        $.loader.hide();
    });
}

function ajaxPostJson(url, data, callback) {
    $.loader.init();
    $.loader.setInfo("Loading...").show();

    $.post(url, data, function (response) {

        $.loader.hide();

        ajaxHandleResponse(url, response, callback);

    }).fail(function (xhr, status, title) {
        $.loader.hide();
    });
}

function form_errors(form, errors) {
    var error_input_found = false;

    form.find(".error-message").remove();

    for (var field in errors) {
        var errs = errors[field];
        var key = "[name='" + field + "']";
        var input = form.find("input" + key);
        var select = form.find("select" + key);

        if (input.length > 0) {
            error_input_found = true;
            for (var e in errs) {
                input.parent().append('<span class="error-message">' + errs[e] + '<span>');
            }
        }

        if (select.length > 0) {
            error_input_found = true;
            for (var e in errs) {
                select.parent().append('<span class="error-message">' + errs[e] + '<span>');
            }
        }

    }

    return error_input_found;
}

$(document).ready(function () {
    $.loader.init();

    $("form").find("div.pristine-error").parents(".form-group").addClass("has-danger");

    $("input[type='checkbox'].chk-select-all").chkSelectAll();

    $(".select2").select2({
        placeHolder: "Please Select",        
        theme: "bootstrap-5",
    });

    $(".fancybox").fancybox();

    $(".date-picker").datepickerExtend();

    $(".date-month-picker").datepickerExtend({
        format: "M-yyyy",
        viewMode: "months",
        minViewMode: "months"
    });

    $(".date-time-picker").datetimepickerExtend();

    $(".css-toggler").cssClassToggle();

    $(".ajax-load").ajaxLoad();

    // $('.time-picker').timepicker({
    //     defaultTime: ""
    // });

    $(".i-data-table").idataTable();

    $(".table-export-csv").srTableCSVExport();

    $(".chk-select-all").chkSelectAll();

    $('input.invalid-char').on('keypress', function (event) {
        var regex = new RegExp("^[a-zA-Z0-9@#$^*()<>{}_.-,]+$");
        var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
        key = key.trim();

        var error_span = $(this).parent().find(".error-message");
        if (error_span.length == 0) {
            $(this).parent().append('<span class="error-message">&#9679 Invalid Character</span>');
            var error_span = $(this).parent().find(".error-message");
        }
        error_span.addClass("hidden");

        if (key && !regex.test(key)) {
            event.preventDefault();

            error_span.removeClass("hidden");

            return false;
        }
    });

    $("form.summary-delete-form").submit(function () {
        var _form = $(this);

        var is_confirm = _form.attr("data-confirm");

        if (!is_confirm) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: constants.swal.button.confirm_color,
                cancelButtonColor: constants.swal.button.cancel_color,
                confirmButtonText: "Yes, delete it!"
            }).then(function (e) {
                if (e.value) {
                    _form.attr("data-confirm", 1);
                    _form.trigger("submit");
                }
            });

            return false;
        }
    });


    $(".clear_form_search_conditions").click(function () {
        var _form = $(this).closest("form");

        _form.find("input[type='text'], input[type='number']").val("");
        _form.find("select").val("").trigger("change");
        _form.find("input[type='checkbox']").prop("checked", false);

        _form.trigger("submit");
    });

    $("form.summary_search").submit(function () {
        $(this).ajaxSubmit({
            target: '#index_table',
            beforeSubmit: function (formData, jqForm, options) {
                $.loader.show();
            },
            success: function (responseText, statusText, xhr, $form) {
                $.loader.hide();
            },
            error: function () {
                $.loader.hide();
            }
        });

        return false;
    });

    $("#index_table").on("click", ".pagination a.page-link, a.sortable", function () {

        var href = $(this).attr("href");
        $.loader.show();

        $("#index_table").load(href, function () {
            $.loader.hide();
        });

        return false;
    });

    $(document).on("click", "a.activate", function () {

        var _this = $(this).parent();
        var href = $(this).attr("href");

        ajaxGetJson(href, function (response) {
            var html = '<span class="badge bg-success">Active</span>';
            html += '<br/>'
            html += '<a class="de_activate" href="' + response['url'] + '">De-Activate</a>';
            _this.html(html);
        });

        return false;
    });

    $(document).on("click", "a.de_activate", function () {

        var _this = $(this).parent();
        var href = $(this).attr("href");

        ajaxGetJson(href, function (response) {
            var html = '<span class="badge bg-danger">De-Active</span>';
            html += '<br/>'
            html += '<a class="activate" href="' + response['url'] + '">Activate</a>';
            _this.html(html);
        });

        return false;
    });

    $(document).on("click", "a.confirm", function () {

        var _this = $(this).parent();
        var href = $(this).attr("href");
        var msg = $(this).data("msg");
        msg = msg ? msg : "Are You Sure?";

        confirmDialog(msg, function(){
            window.location.href = href;
        })

        return false;
    });

    $(document).on("click", "a.confirm_and_popup", function () {

        var _this = $(this).parent();
        var href = $(this).attr("href");
        var msg = $(this).data("msg");
        msg = msg ? msg : "Are You Sure?";

        confirmDialog(msg, function(){
            $.get(href, function(response){                
                $.events.onUserSuccess(response);
            });
        })

        return false;
    });
});


