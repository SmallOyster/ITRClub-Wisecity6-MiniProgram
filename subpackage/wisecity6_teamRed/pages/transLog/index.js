const app = getApp();
var utils = require('../../../../utils/util.js');
var wcUtils = require('../../utils.js');

Page({

  data: {
    StatusBar: app.globalData.StatusBar,
    CustomBar: app.globalData.CustomBar,
    moneyTypeId: 0,
    moneyTypeName: ''
  },

  onLoad: function(opt) {
    if (opt.o == 'm') {
      this.setData({
        moneyTypeId: opt.v,
        moneyTypeName: "筛选币种：" + opt.n
      })
    }
  },

  go: function(opt) {
    utils.collectFormId(opt.detail.formId);

    let url = opt.detail.value.to;
    if (this.data.moneyTypeId != 0) url += '?o=m&v=' + this.data.moneyTypeId;

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