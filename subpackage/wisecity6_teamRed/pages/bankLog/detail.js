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
      url: app.globalData.wisecityApiUrl + "bank.php?mod=detail&teamId=" + wx.getStorageSync('wisecity6_teamId') + "&orderId=" + orderId,
      dataType: 'json',
      success: function(ret) {
        ret = ret.data;
        _this.setData({
          loading: false
        });

        if (ret.code == 200) {
          let data = ret.data;
          let info = data['info'];

          info['type'] = wcUtils.formatBankType(info['type']);

          if (info['type'] == '借贷') {
            if (info['status'] == 0) {
              info['status'] = '已还清';
              info['bgColor'] = '#00DB00';
            } else if (info['status'] == 1) {
              info['status'] = '未还清';
              info['bgColor'] = '#FF2D2D';
            } else if (info['status'] == 2) {
              info['status'] = '申请延期 待审核';
              info['bgColor'] = '#FCFA1E';
            } else if (info['status'] == 3) {
              info['status'] = '申请借贷 待审核';
              info['bgColor'] = '#7B7B7B';
            }
          } else {
            info['status'] = '已结单';
          }

          _this.setData({
            info: info
          });
        } else if (ret.code == 404) {
          _this.setData({
            modalName: 'tipsModal'
          })
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

  tips404: function(opt) {
    utils.collectFormId(opt.detail.formId);
    wx.redirectTo({
      url: 'list'
    })
  }
})