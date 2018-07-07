/**
 * Created by peter on 2018/6/2.
 */

var cart = {
    addEName: '.btnCartAdd',
    add: function (goodsId, specId,buyNum) {
        if (common.isEmpty(goodsId)) {
            return false;
        }
        buyNum = common.isEmpty(buyNum) ? 1 : buyNum;

        var postData = {goods_id: goodsId, buy_num: buyNum,spec_id : specId};
        console.log(postData)

        $.post("/cart/add", postData, function (responseData) {
            if (!responseData.result) {
                swAlert.warning(responseData.message);
                return false;
            } else {

            }
        }, 'JSON');
    },
    remove: function (goodsId) {

    },
    clear: function () {

    },
    sync: function () {

    },
    init: function () {
        $(this.addEName).on(function () {
            alert('asdfadsf');
        });
    }
};
cart.init();