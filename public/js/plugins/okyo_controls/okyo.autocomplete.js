/**
 * 带输入的选择框
 * v 0.1b
 * 调用
 * var data = [{"id": 1, "name": "黄金日", "pinyin": "huangjinri", "sp": "hjr"}, {"id": 2, "name": "天通金", "pinyin": "tiantongjing", "sp": "ttj"}, {"id": 3, "name": "天津贵金属", "pinyin": "tianjingguijingsu", "sp": "tjgjs"}];
 * $('#demo').okyoInputSelect({"data": data, "defValue": "黄金日"});
 */
(function($) {
    var defualts = {
        dataUrl: '',
        optionWidth: null,
        data: null,
        defValue: '',
        allowInput: true
    };

    $.fn.okyoAutoComplete = function(options) {
        var opts = $.extend({}, defualts, options);
        var obj = $(this);
        var objBtn = $('<a href="javascript:void(0);" class="btnSelect"></a>');
        var objOptions = $('<div class="options"></div>');
        var optionMod = '<a href="javascript:void(0);" class="option" data-id=""></a>';
        var init = function() {
            obj.css({"padding-right": "20px"});
            obj.wrap('<div class="uic_sibox"></div>');
            obj.before(objOptions);
            obj.before(objBtn);
            if (!opts.allowInput) {
                obj.attr('readyonly', true);
            }
            if (opts.defValue !== '') {
                obj.val(opts.defValue);
            }
            if (isNaN(parseInt(opts.optionWidth))) {
                opts.optionWidth = obj.outerWidth();
            }
            objOptions.css({
                "width": opts.optionWidth,
                "top": obj.height() + 1
            });
            if (opts.data !== null) {
                $.each(opts.data, function(index, value) {
                    var objTemp = $(optionMod);
                    $.each(value, function(key, val) {
                        objTemp.attr("data-" + key, val);
                    });
                    objTemp.text(value.name);
                    objTemp.appendTo(objOptions);
                    objTemp.bind('click', function() {
                        selected($(this).text());
                    });
                });
            }
        };
        var selected = function(val) {
            obj.val(val);
            closeOptions();
        };

        var closeOptions = function() {
            $('body').unbind('click', closeHandle);
            objOptions.fadeOut(100);
        };
        var openOptions = function() {
            if (obj.val() == '') {
                $("a.option").css('display', 'block');
            }
            objOptions.slideDown(250, function() {
                $('body').bind('click', closeHandle);
            });
        };

        var closeHandle = function(e) {
            if ($(e.target).parents('.uic_sibox').length === 0) {
                closeOptions();
            }
        };

        //初始化
        init();


        //事件绑定
        objBtn.bind('click', function() {
            openOptions();
        });

        obj.bind('keyup', function(e) {
            var key = obj.val();
            if (key == '') {
                openOptions();
                return false;
            }
            if ($("a.option[data-pinyin^=" + key + "]").length > 0 || $("a.option[data-sp^=" + key + "]").length > 0) {
                openOptions();
            } else {
                closeOptions();
            }
            $(".uic_sibox .option").css('display', 'none');
            $("a.option[data-pinyin^=" + key + "]").css('display', 'block');
            $("a.option[data-sp^=" + key + "]").css('display', 'block');
        });
        obj.click(function() {
            obj.select();
            obj.focus();
        });

        var om = {};
        return om;
    };
})(jQuery);