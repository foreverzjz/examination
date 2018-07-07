/**
 * Created by Administrator on 2016/10/10.
 */
var common = {
    random: function () {
        return new Date().getTime();
    },
    isEmpty: function (value) {
        if (value == '' || value == undefined || value == 0 || value == null) {
            return true;
        } else {
            return false;
        }
    },
    O2String: function (O) {
        var S = [];
        var J = "";
        if (Object.prototype.toString.apply(O) === '[object Array]') {
            for (var i = 0; i < O.length; i++)
                S.push(this.O2String(O[i]));
            J = '[' + S.join(',') + ']';
        }
        else if (Object.prototype.toString.apply(O) === '[object Date]') {
            J = "new Date(" + O.getTime() + ")";
        }
        else if (Object.prototype.toString.apply(O) === '[object RegExp]' || Object.prototype.toString.apply(O) === '[object Function]') {
            J = O.toString();
        }
        else if (Object.prototype.toString.apply(O) === '[object Object]') {
            for (var i in O) {
                O[i] = typeof (O[i]) == 'string' ? '"' + O[i] + '"' : (typeof (O[i]) === 'object' ? this.O2String(O[i]) : O[i]);
                S.push(i + ':' + O[i]);
            }
            J = '{' + S.join(',') + '}';
        }

        return J;
    },
    addBookmark: function (title, url) {
        if (document.all) {
            window.external.addFavorite(url, title);
        }
        else if (window.sidebar) {
            window.sidebar.addPanel(title, url, "");
        }
    },
    timeoutJumpUrl: function (url, timeLimit, frame) {
        setTimeout(function () {
            if (common.isEmpty(frame)) {
                self.location = url;
            } else {
                frame.location = url;
            }
        }, timeLimit)
    },
    /**
     * 字符串填充
     * @param string str 要进行填充的字符串
     * @param int len    目标字符串长度
     * @param str chr    用于填充的字符 默认为空格
     * @param str dir    填充位置 left|right|both 默认为right
     */
    strPad: function (str, len, chr, dir) {
        str = str.toString();
        len = (typeof len == 'number') ? len : 0;
        chr = (typeof chr == 'string') ? chr : ' ';
        dir = (/left|right|both/i).test(dir) ? dir : 'right';
        var repeat = function (c, l) {

            var repeat = '';
            while (repeat.length < l) {
                repeat += c;
            }
            return repeat.substr(0, l);
        }
        var diff = len - str.length;
        if (diff > 0) {
            switch (dir) {
                case 'left':
                    str = '' + repeat(chr, diff) + str;
                    break;
                case 'both':
                    var half = repeat(chr, Math.ceil(diff / 2));
                    str = (half + str + half).substr(1, len);
                    break;
                default:
                    str = '' + str + repeat(chr, diff);
            }
        }
        return str;
    },
    datetime2Unix: function (datetime) {
        var tmp_datetime = datetime.replace(/:/g, '-');
        tmp_datetime = tmp_datetime.replace(/ /g, '-');
        var arr = tmp_datetime.split("-");
        if (common.isEmpty(arr[3])) {
            arr[3] = '00'
        }
        if (common.isEmpty(arr[4])) {
            arr[4] = '00'
        }
        if (common.isEmpty(arr[5])) {
            arr[5] = '00'
        }
        var now = new Date(Date.UTC(arr[0], arr[1] - 1, arr[2], arr[3] - 8, arr[4], arr[5]));
        return parseInt(now.getTime() / 1000);
    },

    /**
     * 格式化日期
     * 类似php Date函数，传入Unix 时间戳（秒级）返回指定格式
     * 格式(不区分大小写)：
     * y 表示4位年份
     * m 表示2位月份
     * d 表示2位日
     * h 表示2位时
     * i 表示2位分
     * s 表示2位秒
     */
    formatDate: function (format, timestamp) {
        var date = new Date(parseInt(timestamp) * 1000);
        var year = date.getFullYear();
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var hour = date.getHours();
        var minute = date.getMinutes();
        var second = date.getSeconds();
        month = common.strPad(month, 2, '0', 'left');
        day = common.strPad(day, 2, '0', 'left');
        hour = common.strPad(hour, 2, '0', 'left');
        minute = common.strPad(minute, 2, '0', 'left');
        second = common.strPad(second, 2, '0', 'left');
        format = format.replace(/y/gi, year);
        format = format.replace(/m/gi, month);
        format = format.replace(/d/gi, day);
        format = format.replace(/h/gi, hour);
        format = format.replace(/i/gi, minute);
        format = format.replace(/s/gi, second);
        return format;
    },
    /**
     */
    getDateTime: function (strDateTime) {
        var tmpTime = Date.parse(strDateTime);
        if (isNaN(tmpTime)) {
            var arr = strDateTime.split(/[- :]/);
            tmpTime = new Date(arr[0], arr[1] - 1, arr[2], arr[3], arr[4], arr[5]);
        }

        return parseInt(tmpTime / 1000);
    },
    /**
     * @param intTime 整数时间
     * @param format 格式
     * @param timeUnit 传入参数时间单位 h/m/s(时/分/秒)
     */
    formatTime: function (intTime, format, timeUnit) {
        format = this.isEmpty(format) ? '%h小时%m分钟' : format;
        timeUnit = this.isEmpty(timeUnit) ? 'm' : timeUnit;
        var isMinus = intTime < 0 ? true : false;
        intTime = Math.abs(intTime);
        switch (timeUnit.toLowerCase()) {
            case 'h':
                intTime = intTime * 3600;
                break;
            case 's':
                break;
            default:
                intTime = intTime * 60;
                break;
        }
        var hour = parseInt(intTime / 3600);
        var minute = parseInt(parseInt(intTime % 3600) / 60);
        var second = intTime - (hour * 3600 + minute * 60);
        var text = format;
        if (hour == 0) {
            text = format.substring(format.indexOf('%m'));
        }
        if (hour == 0 && minute == 0) {
            text = format.substring(format.indexOf('%s'));
        }
        if (hour == 0 && minute == 0 && second == 0) {
            var lastFormat = text.lastIndexOf('%');
            text = text.substring(lastFormat);
        }
        text = text.replace('%h', hour).replace('%m', minute).replace('%s', second);
        if (isMinus) {
            return '- ' + text;
        } else {
            return text;
        }
    },
    timeOut: function (endTime, nowTime) {
        var timeDiff = endTime - nowTime;
        var day, hour, minute, second;
        var returnValue = {d: 0, h: 0, m: 0, s: 0};
        if (timeDiff <= 0) {
            return false;
        }
        var returnString = '';
        if (timeDiff >= 86400) {
            day = parseInt(timeDiff / 86400);
            timeDiff = timeDiff % 86400;
        }
        if (timeDiff >= 3600) {
            hour = parseInt(timeDiff / 3600);
            timeDiff = timeDiff % 3600;
        }
        if (timeDiff >= 60) {
            minute = parseInt(timeDiff / 60);
            timeDiff = timeDiff % 60;
        }
        second = timeDiff;
        if (!common.isEmpty(day)) {
            returnValue.d = day;
        }
        if (!common.isEmpty(hour)) {
            returnValue.h = hour;
        }
        if (!common.isEmpty(minute)) {
            returnValue.m = minute;
        }
        if (!common.isEmpty(second)) {
            returnValue.s = second;
        }
        return returnValue;

    },
    timeAgo: function (startTime, endTime) {
        var intStartTime = 0;
        var intEndTime = 0;
        if (!new RegExp(regexEnum.intege).test(startTime)) {
            intStartTime = Date.parse(new Date(startTime.replace(/-/g, '/'))) / 1000;
        } else {
            intStartTime = parseInt(startTime);
        }
        if (endTime == undefined) {
            intEndTime = Date.parse(new Date()) / 1000;
        } else {
            if (!new RegExp(regexEnum.intege).test(endTime)) {
                intEndTime = Date.parse(new Date(endTime.replace(/-/g, '/'))) / 1000;
            } else {
                intEndTime = parseInt(endTime);
            }
        }
        var timeDiff = intEndTime - intStartTime;
        if (timeDiff <= 0) {
            return '未开始';
        }
        retrun = '';
        // if (timeDiff >= 259200) {
        //     retrun = parseInt(timeDiff / 86400) + '天';
        // } else if (timeDiff >= 172800) {
        //     retrun = "前天 " + common.formatDate('H:i', time);
        // } else if (timeDiff >= 86400) {
        //     retrun = "昨天" + common.formatDate('H:i', time);
        // } else if (timeDiff >= 3600) {
        if (timeDiff >= 3600) {
            hour = parseInt(timeDiff / 3600);
            minute = parseInt((timeDiff % 3600) / 60);
            retrun = hour + '小时';
            if (minute > 0) {
                retrun += minute + '分';
            }
        } else if (timeDiff >= 60) {
            minute = parseInt(timeDiff / 60);
            second = timeDiff % 60;
            retrun = minute + '分';
            if (second > 0) {
                retrun += second + '秒';
            }
        } else {
            retrun = timeDiff + '秒';
        }
        return retrun;
    },
    numberFormat: function (number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number
        var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
        var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
        var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
        var s = '';
        var toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };

        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');

        var rega = /^(\+|-)?(\d+)(\.\d+)?$/;

        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }

        return s.join(dec);
    },
    ceil: function (number, decimals) {
        var k = Math.pow(10, decimals);
        return Math.ceil(number * k) / k;
    },
    round: function (number, decimals) {
        var k = Math.pow(10, decimals);
        return Math.round(number * k) / k;
    },
    compare: function (x, y) {//比较函数
        if (x < y) {
            return -1;
        } else if (x > y) {
            return 1;
        } else {
            return 0;
        }
    }
}

var regexEnum =
    {
        intege: "^-?[1-9]\\d*$",					//整数
        intege1: "^[1-9]\\d*$",					//正整数
        intege2: "^-[1-9]\\d*$",					//负整数
        num: "^([+-]?)\\d*\\.?\\d+$",			//数字
        num1: "^[1-9]\\d*|0$",					//正数（正整数 + 0）
        num2: "^-[1-9]\\d*|0$",					//负数（负整数 + 0）
        decmal: "^([+-]?)\\d*\\.\\d+$",			//浮点数
        decmal1: "^[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*$",　　	//正浮点数
        decmal2: "^-([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*)$",　 //负浮点数
        decmal3: "^-?([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*|0?.0+|0)$",　 //浮点数
        decmal4: "^[1-9]\\d*.\\d*|0.\\d*[1-9]\\d*|0?.0+|0$",　　 //非负浮点数（正浮点数 + 0）
        decmal5: "^(-([1-9]\\d*.\\d*|0.\\d*[1-9]\\d*))|0?.0+|0$",　　//非正浮点数（负浮点数 + 0）
        email: "^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$", //邮件
        color: "^[a-fA-F0-9]{6}$",				//颜色
        url: "^http[s]?:\\/\\/([\\w-]+\\.)+[\\w-]+([\\w-./?%&=]*)?$",	//url
        chinese: "^[\\u4E00-\\u9FA5\\uF900-\\uFA2D]+$",					//仅中文
        truename: "^([\\u4E00-\\u9FA5\\uF900-\\uFA2D]+|[A-Za-z\\s]+)$",	//真实姓名，中文及英文字母
        nickname: "^([\\u4E00-\\u9FA5\\uF900-\\uFA2D]+|\\w+$)$",			//昵称，中文，英文字母_
        ascii: "^[\\x00-\\xFF]+$",				//仅ACSII字符
        zipcode: "^\\d{6}$",						//邮编
        //mobile:"^13[0-9]{9}|15[012356789][0-9]{8}|18[0256789][0-9]{8}|147[0-9]{8}$",				//手机
        mobile: "^1[0-9]{10}$",				//手机
        ip4: "^(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)\\.(25[0-5]|2[0-4]\\d|[0-1]\\d{2}|[1-9]?\\d)$",	//ip地址
        notempty: "^\\S+$",						//非空
        picture: "(.*)\\.(jpg|bmp|gif|ico|pcx|jpeg|tif|png|raw|tga)$",	//图片
        rar: "(.*)\\.(rar|zip|7zip|tgz)$",								//压缩文件
        date: "^\\d{4}(\\-|\\/|\.)\\d{1,2}\\1\\d{1,2}$",					//日期
        qq: "^[1-9]*[1-9][0-9]*$",				//QQ号码
        telephone: "^((([0\\+]\\d{2,3}-)?(0\\d{2,3})-)?(\\d{7,8})(-(\\d{3,}))?|13[0-9]{9}|15[012356789][0-9]{8}|18[0256789][0-9]{8}|147[0-9]{8})$",
        tel: "^(([0\\+]\\d{2,3}-)?(0\\d{2,3})-)?(\\d{7,8})(-(\\d{3,}))?$", //电话号码的函数(包括验证国内区号,国际区号,分机号)
        username: "^\\w+$",						//用来用户注册。匹配由数字、26个英文字母或者下划线组成的字符串
        letter: "^[A-Za-z]+$",					//字母
        letter_u: "^[A-Z]+$",					//大写字母
        letter_l: "^[a-z]+$",					//小写字母
        idcard: "^(\\d{18,18}|\\d{15,15}|\\d{17,17}x)$"	//身份证
    }
//计算字符串长度
String.prototype.strLen = function () {
    var len = 0;
    for (var i = 0; i < this.length; i++) {
        if (this.charCodeAt(i) > 255 || this.charCodeAt(i) < 0)
            len += 2;
        else
            len++;
    }
    return len;
}
//将字符串拆成字符，并存到数组中
String.prototype.strToChars = function () {
    var chars = new Array();
    for (var i = 0; i < this.length; i++) {
        chars[i] = [this.substr(i, 1), this.isCHS(i)];
    }
    String.prototype.charsArray = chars;
    return chars;
}
//判断某个字符是否是汉字
String.prototype.isCHS = function (i) {
    if (this.charCodeAt(i) > 255 || this.charCodeAt(i) < 0)
        return true;
    else
        return false;
}
//截取字符串（从start字节到end字节）
String.prototype.subCHString = function (start, end) {
    var len = 0;
    var str = "";
    this.strToChars();
    for (var i = 0; i < this.length; i++) {
        if (this.charsArray[i][1])
            len += 2;
        else
            len++;
        if (end < len)
            return str + '...';
        else if (start < len)
            str += this.charsArray[i][0];
    }
    return str;
}
//截取字符串（从start字节截取length个字节）
String.prototype.subCHStr = function (start, length) {
    return this.subCHString(start, start + length);
}