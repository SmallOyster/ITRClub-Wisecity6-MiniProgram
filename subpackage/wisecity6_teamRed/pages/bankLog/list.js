const app = getApp();
var utils = require('../../../../utils/util.js');
var wcUtils = require('../../utils.js');

Page({

  data: {
    StatusBar: app.globalData.StatusBar,
    CustomBar: app.globalData.CustomBar,
    loading: false,
    modalName: null,
    tipsContent: '',
    sessionId: ''
  },

  onLoad: function(options, sec = '') {
    let _this = this;
    let url = app.globalData.wisecityApiUrl + "bank.php?mod=list&teamId=" + wx.getStorageSync('wisecity6_teamId');
    let orderByData = {};

    _this.setData({
      loading: true
    });

    if (options.o == 'm') {
      orderByData.m = options.v;

      if (_this.data.orderByM == '' || _this.data.orderByM == undefined) {
        _this.setData({
          orderByM: options.v
        })
      }
    }
    if (sec != '') {
      orderByData.sec = sec;
    }

    if (orderByData != {}) url += '&orderBy=' + JSON.stringify(orderByData);
    if (this.data.sessionId != '') url += '&sid=' + this.data.sessionId;

    wx.request({
      url: url,
      dataType: 'json',
      success: function(ret) {
        ret = ret.data;
        _this.setData({
          loading: false
        });

        if (ret.code == 200) {
          let data = ret.data;
          let list = data['list'];

          for (let i in list) {
            list[i]['type'] = wcUtils.formatBankType(list[i]['type']);

            if (list[i]['type'] == '借贷') {
              if (list[i]['status'] == 0) list[i]['bgColor'] = '#00DB00';
              else if (list[i]['status'] == 1) list[i]['bgColor'] = '#FF2D2D';
              else if (list[i]['status'] == 2) list[i]['bgColor'] = '#FCFA1E';
              else if (list[i]['status'] == 3) list[i]['bgColor'] = '#7B7B7B';
            } else {
              list[i]['bgColor'] = '#333';
            }
          }

          _this.setData({
            list: list,
            sessionId: data['sessionId']
          });
        }
      }
    })
  },


  collectFID: function(opt) {
    utils.collectFormId(opt.detail.formId);

    if (opt.detail.value.id) {
      wx.redirectTo({
        url: 'detail?v=' + opt.detail.value.id,
      })
    } else if (opt.detail.value.wcNavToUrl && opt.detail.value.wcNavToUrl != '../transLog/index') {
      wx.redirectTo({
        url: opt.detail.value.wcNavToUrl,
      })
    } else if (opt.detail.value.back && opt.detail.value.back == 1) {
      wx.navigateBack({})
    }
  },

  toSort: function(opt) {
    let v = opt.detail.value.o;
    let _this = this;

    if (this.data.orderByM != undefined) this.onLoad({
      'o': 'm',
      'v': _this.data.orderByM
    }, v);
    else this.onLoad({}, v);
  }
})