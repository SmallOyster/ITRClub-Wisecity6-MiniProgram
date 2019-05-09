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

  go: function(opt) {
    utils.collectFormId(opt.detail.formId);

    let url = opt.detail.value.to;
    wx.navigateTo({
      url: url,
    })
  },

  collectFID: function(opt) {
    utils.collectFormId(opt.detail.formId);

    if (opt.detail.value.wcNavToUrl && opt.detail.value.wcNavToUrl != '../transLog/index') {
      wx.redirectTo({
        url: opt.detail.value.wcNavToUrl,
      })
    }
  },
})