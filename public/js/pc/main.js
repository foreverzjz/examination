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
            buttons: false,
            content: {
                element: "div",
                attributes: {
                    innerHTML: '<div><i class="fa fa-spinner fa-spin mr-2"></i> Loading ...</div>'
                },
                innerHTML: 'content'
            }
        });
    },
    close: function () {
        swal.close();
    }
};

var ajaxFormCallback = {
    validate: function () {
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
        beforeSubmit: ajaxFormCallback.validate,
        success: ajaxFormCallback.requestSuccess
    };

    formOption.target.ajaxSubmit(formOption);
    return false;
});


var fitHeader = {
    topLimit: 85,
    objTopH: '#barTop',
    objHeader: '.panel-header',
    showTop: true,
    miniStatus: false,
    miniOuterHeight: 86,
    outerHeight: 116,
    leftFixedColumn: 190,
    fixedHeader: function () {
        $('#panelHeader').animate({
            height: fitHeader.miniOuterHeight + "px"
        }, 300);
        $(this.objHeader).css({
            top: 0,
            left: 0
        }).addClass('position-fixed').animate({
            "padding-top": "5px",
            "padding-bottom": "5px"
        }, 300, function () {
            $('#FixedColumn').addClass('position-fixed').css({
                top: fitHeader.miniOuterHeight + "px"
            });
        });
        $(this.objHeader + ' .logo img').animate({
            width: "75%",
            'margin-top': "5px"
        }, 300);

        this.showTop = false;
    },
    staticHeader: function () {
        $('#panelHeader').animate({
            height: fitHeader.outerHeight + "px"
        }, 300);
        $(this.objHeader).removeClass('position-fixed').animate({
            "padding-top": "25px",
            "padding-bottom": "25px"
        }, 300, function () {
            $('#panelHeader').height('auto');
        });
        $(this.objHeader + ' .logo img').animate({
            width: "100%",
            'margin-top': "0px"
        }, 300);
        $('#FixedColumn').removeClass('position-fixed');
        this.showTop = true;
    },
    init: function () {
        this.outerHeight = $('#panelHeader').outerHeight(true);
        this.leftFixedColumn = $('#FixedColumn').offset().top;
        $('#FixedColumn').css({
            height:$(window).height() * 0.5 + 'px'
        });
        $(window).scroll(function () {
            if (fitHeader.showTop && $(window).scrollTop() > $(fitHeader.objTopH).height()) {
                fitHeader.fixedHeader();
            }
            if (!fitHeader.showTop && $(window).scrollTop() <= $(fitHeader.objTopH).height()) {
                fitHeader.staticHeader();
            }

            // if ($('#FixedColumn').position().top < 130) {
            //     $('#FixedColumn').addClass('position-fixed').css({
            //         top: "80px"
            //     });
            // } else {
            //     $('#FixedColumn').removeClass('position-fixed');
            // }
        });
    }
};
