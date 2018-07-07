//全局

var swAlert = {
    info: function (message, title) {
        swal({
            title: title,
            text: message,
            allowOutsideClick: true,
        });
    },
    success: function (message, title) {
        swal({
            title: title,
            text: message,
            icon: "success",
            allowOutsideClick: true,
        });
    },
    warning: function (message, title) {
        swal({
            title: title,
            text: message,
            icon: "warning",
        });
    },
    error: function (message, title) {
        swal({
            title: title,
            text: message,
            icon: "error",
        });
    },
    confirm: function (message, title, confirmOption, cancelOption) {
        var optsConfirm = {
            text: "确认",
            value: true,
            visible: true,
            className: "btn-primary",
            closeModal: true,
            callback: null
        };
        var optsCancel = {
            text: "取消",
            value: false,
            visible: true,
            className: "",
            closeModal: true,
            callback: null
        };

        $.extend(optsConfirm, confirmOption);
        $.extend(optsCancel, cancelOption);


        swal({
            title: title,
            text: message,
            icon: "warning",
            buttons: {
                cancel: optsCancel,
                confirm: optsConfirm,
            },
            closeModal: true
        }).then((isConfirm) => {
            if (isConfirm) {
                if ($.isFunction(optsConfirm.callback)) {
                    optsConfirm.callback();
                }
            } else {
                if ($.isFunction(optsCancel.callback)) {
                    optsCancel.callback();
                }
            }
        });
    },
    loading: function () {
        swal({
            buttons:false,
            content: {
                element: "div",
                attributes: {
                    innerHTML:'<div><i class="fa fa-spinner fa-spin mr-2"></i> Loading ...</div>'
                },
                innerHTML :'content'
            }
        });
    },
    close: function () {
        swal.close();
    }
};


var ajaxFormCallback = {
    before: function () {
        return true;
    },
    success: function (data) {
        return true;
    },
    requestSuccess: function (responseData) {
        if (!$.isPlainObject(responseData)) {
            swAlert.error("数据解析错误！");
            return false;
        }
        if (!responseData.result) {
            swAlert.error(responseData.message);
            return false;
        }
        ajaxFormCallback.success(responseData.data);
    },
    error: function (a, b, c) {
        console.log(a);
        console.log(b);
        console.log(c);
        swAlert.info("请求失败(" + b + ")，请稍候再试！");
    }
};


$(".ajaxForm").submit(function () {
    var formOption = {
        target: $(this),
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        dataType: 'json',
        timeout: 3000,
        forceSync: false,
        data: {},
        error: ajaxFormCallback.error,
        beforeSubmit: ajaxFormCallback.before,
        success: ajaxFormCallback.requestSuccess
    };

    formOption.target.ajaxSubmit(formOption);
    return false;
});


//框架JS
$('#frame-sidebar').find('.menu-item > a').on('click', function () {
    var $menuItem = $(this).parents('.menu-item');

    $menuItem.siblings().removeClass('current').find('.menu-sub').slideUp();
    $menuItem.find('.menu-sub').stop().slideToggle(function () {
        $menuItem.addClass('current');
    });
});

$('.jsBtnLogout').on('click', function () {
    $.get('/mc/manager/logout', {}, function (responseData) {
        if (!$.isPlainObject(responseData)) {
            swAlert.error("数据解析错误！");
            return false;
        }
        if (!responseData.result) {
            swAlert.error(responseData.message);
            return false;
        }
        window.top.location = '/mc';
    }, 'json');
});

$.ajaxSetup({
    error: function (a, b, c) {
        console.log(a);
        console.log(b);
        console.log(c);
        swAlert.info("请求失败(" + b + ")，请稍候再试！");
    }
});


// swal({
//     title: "Are you sure?",
//     text: "Once deleted, you will not be able to recover this imaginary file!",
//     icon: "warning",
//     buttons: true,
//     dangerMode: true,
// }).then((willDelete) => {
//     if (willDelete) {
//         swal("Poof! Your imaginary file has been deleted!", {
//             icon: "success",
//         });
//     } else {
//         swal("Your imaginary file is safe!");
//     }
// });
