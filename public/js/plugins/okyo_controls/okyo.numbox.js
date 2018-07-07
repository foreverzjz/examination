(function($) {
    var defualts = {
        maxNum: 50,
        minNum: 0,
        defaultNum: 1,
        bindEvent: 'keyup',
        tipBox: null,
        isByteLen: false,
        hideOverflow: false,
        step: 1,
		changeEvent:null
    };

    $.fn.okyoNumBox = function(options) {
        var opts = $.extend({}, defualts, options);
        var obj = $(this);
        var objUp = $('<a href="javascript:void(0);" class="uic_btn_up"><i class="fa fa-caret-up"></i></a>');
        var objDown = $('<a href="javascript:void(0);" class="uic_btn_down"><i class="fa fa-caret-down"></i></a>');
        var init = function() {
            if (opts.defaultNum != null) {
                setValue(opts.defaultNum);
            }
            obj.wrap('<div class="uic_numbox"></div>');
            obj.before(objDown);
            obj.before(objUp);
            obj.attr('onpaste', 'return false;');
        };
        var setValue = function(num) {
			if(isNaN(num)){
				num = '';
			}else{
				if (num >= opts.maxNum) {
					num = opts.maxNum;
					objUp.addClass('uic_btn_disabled');
				} else {
					objUp.removeClass('uic_btn_disabled');
				}
				if (num <= opts.minNum) {
					num = opts.minNum;
					objDown.addClass('uic_btn_disabled');
				} else {
					objDown.removeClass('uic_btn_disabled');
				}
			}
            obj.val(num);
			if($.isFunction(opts.changeEvent)){
                opts.changeEvent(num);
            }
            obj.trigger('focus');
        };

        //初始化
        init();

        //事件绑定
        obj.bind('keypress', function(e) {
            if (e.ctrlKey || e.shiftKey) {
                return false;
            }
            if (e.keyCode >= 48 && e.keyCode <= 57) {
                return e.keyCode - 48;
            } else {
                return false;
            }
        });
        obj.click(function() {
            obj.select();
            obj.focus();
        });
        obj.bind('change', function() {
            setValue(parseInt(obj.val()));
        });
        $('.uic_btn_up,.uic_btn_down').hover(function() {
            $(this).addClass('uic_btn_event_over');
        }, function() {
            $(this).removeClass('uic_btn_event_over');
        }).mousedown(function() {
            $(this).addClass('uic_btn_event_down');
        }).mouseup(function() {
            $(this).removeClass('uic_btn_event_down');
        });
        objUp.click(function() {
            var value = isNaN(parseInt(obj.val())) ? opts.minNum : parseInt(obj.val()) + opts.step;
            if ($(this).hasClass('uic_btn_disabled')) {
                return false;
            }
            setValue(value);
        });
        objDown.click(function() {
            if ($(this).hasClass('uic_btn_disabled')) {
                return false;
            }
            var value = isNaN(parseInt(obj.val())) ? opts.maxNum : parseInt(obj.val()) - opts.step;
            setValue(value);
        });


        var om = {};
        om.setMax = function(num) {
            opts.maxNum = num;
        };
        om.setMin = function(num) {
            opts.minNum = num;
        };
        om.setDefault = function(num) {
            opts.defaultNum = num;
            setValue(num);
        };
        om.disabled = function(isClear) {
            if(isClear === true){
                obj.val('');
            }
            objUp.addClass('uic_btn_disabled');
            objDown.addClass('uic_btn_disabled');
            obj.prop("disabled", true);
        };
        om.enable = function(defaultNum) {
            if(!isNaN(parseInt(defaultNum))){
                setValue(defaultNum);
            }
            objUp.removeClass('uic_btn_disabled');
            objDown.removeClass('uic_btn_disabled');
            obj.prop("disabled", false);
        };

        return om;
    };
})(jQuery);
