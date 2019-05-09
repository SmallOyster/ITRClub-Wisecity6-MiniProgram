const app = getApp();
var utils = require('../../../../utils/util.js');

Page({

  data: {
    StatusBar: app.globalData.StatusBar,
    CustomBar: app.globalData.CustomBar,
    loading: false,
    modalName: null,
    tipsContent: '',
    roleName: wx.getStorageSync('wisecity6_role'),
    realName: wx.getStorageSync('wisecity6_realName'),
  },

  onLoad: function(options) {

  },

  unbind: function(opt) {
    let _this = this;
    _this.setData({
      loading: true
    });

    utils.collectFormId(opt.detail.formId);

    wx.request({
      url: app.globalData.wisecityApiUrl + 'bindUser.php',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      data: {
        'mod': 'unbind',
        'openId': wx.getStorageSync('openId')
      },
      method: 'post',
      dataType: 'json',
      success: function(ret) {
        ret = ret.data;
        _this.setData({
          loading: false
        });

        if (ret.code == 200) {
          wx.showModal({
            title: '温馨提示',
            content: '解绑成功！欢迎您再次使用！',
            showCancel: false,
            success(res) {
              if (res.confirm) {
                wx.removeStorageSync('wisecity6_navInfo');
                wx.removeStorageSync('wisecity6_role');
                wx.removeStorageSync('wisecity6_teamId');
                wx.removeStorageSync('wisecity6_teamName');
                wx.removeStorageSync('wisecity6_groupName');
                wx.removeStorageSync('wisecity6_groupId');

                wx.switchTab({
                  url: '/pages/function/index',
                })
              }
            }
          })
        } else {
          wx.showModal({
            title: '温馨提示',
            content: '解绑失败！错误码：' + ret.code,
            showCancel: false
          })
        }
      }
    })
  },

  collectFID: function(opt) {
    utils.collectFormId(opt.detail.formId);

    if (this.data.modalName == 'tipsModal') {
      this.setData({
        modalName: null
      })
    } else if (opt.detail.value.wcNavToUrl && opt.detail.value.wcNavToUrl != '../my/list') {
      wx.redirectTo({
        url: opt.detail.value.wcNavToUrl,
      })
    }
  },
})