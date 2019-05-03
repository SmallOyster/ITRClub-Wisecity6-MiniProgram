const app = getApp();
var utils = require('../../../../utils/util.js');
var wcUtils = require('../../utils.js');

Page({

  data: {
    StatusBar: app.globalData.StatusBar,
    CustomBar: app.globalData.CustomBar,
    loading: false,
    modalName: null,
    tipsContent: ''
  },

  onLoad: function(options) {
    let orderId = options.v;
    let _this = this;

    _this.setData({
      loading: true,
      orderId: orderId
    });

    wx.request({
      url: app.globalData.wisecityApiUrl + "team/transaction.php?mod=detail&teamId=" + wx.getStorageSync('wisecity6_teamId') + "&orderId=" + orderId,
      dataType: 'json',
      success: function(ret) {
        ret = ret.data;
        _this.setData({
          loading: false
        });

        if (ret.code == 200) {
          let data = ret.data;
          let info = data['info'];

          info['type'] = wcUtils.formatTransactionType(info['type']);

          _this.setData({
            info: info
          });
        }
      }
    });
  },

  collectFID: function(opt) {
    utils.collectFormId(opt.detail.formId);

    if (this.data.modalName == 'tipsModal') {
      this.setData({
        modalName: null
      })
    } else if (opt.detail.value.back == 1) {
      wx.redirectTo({
        url: 'list'
      })
    }
  },
})