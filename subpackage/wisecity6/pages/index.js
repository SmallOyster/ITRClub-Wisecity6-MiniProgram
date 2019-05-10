const app = getApp();
var utils = require('../../../utils/util.js');
var wcUtils = require('../util.js');

Page({

  timing: '',

  data: {
    StatusBar: app.globalData.StatusBar,
    CustomBar: app.globalData.CustomBar,
    wcNavData: wcUtils.getwcNavInfo()
  },

  onLoad: function(options) {
    this.setData({
      loading: true
    });

    if (wx.getStorageSync('wisecity6_role') == '') {
      wx.redirectTo({
        url: 'bindUser',
      })
    }

    this.getNowFinanceYear();

    if (wx.getStorageSync('wisecity6_role') == 'group') {
      var roleName = '商帮';
      var teamName = ' /';
      var groupName = wx.getStorageSync('wisecity6_groupName');
    } else if (wx.getStorageSync('wisecity6_role') == 'admin') {
      var roleName = '主席团';
      var teamName = ' /';
      var groupName = ' /';
    } else {
      var roleName = '参赛队伍';
      var teamName = wx.getStorageSync('wisecity6_teamName');
      var groupName = wx.getStorageSync('wisecity6_groupName');
    }

    this.setData({
      roleName: roleName,
      teamName: teamName,
      groupName: groupName,
      loading: false
    })
  },

  collectFID: function(opt) {
    utils.collectFormId(opt.detail.formId);

    if (opt.detail.value.wcNavToUrl && opt.detail.value.wcNavToUrl != 'index') {
      if (opt.detail.value.wcNavToUrl == 'functionTabbar') {
        wx.switchTab({
          url: '/pages/function/index',
        })
      } else {
        wx.redirectTo({
          url: opt.detail.value.wcNavToUrl,
        })
      }
    } else if (this.data.modalName == 'tipsModal') {
      this.setData({
        modalName: null
      })
    }
  },

  unbind: function(opt) {
    utils.collectFormId(opt.detail.formId);
    this.setData({
      modalName: 'confirmModal'
    })
  },
  cancelUnbind: function(opt) {
    utils.collectFormId(opt.detail.formId);
    this.setData({
      modalName: ''
    })
  },

  sureUnbind: function(opt) {
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

  getNowFinanceYear: function() {
    let _this = this;
    wx.request({
      url: app.globalData.wisecityApiUrl + 'financeYear.php?mod=now',
      success: function(ret) {
        let data = ret.data;
        let msg = '';
        let remainTime = '';

        if (data.code == 200) {
          data = data.data.list;
          msg = "当前财年：第 " + data['num'] + " 财年";
          _this.compareTime(utils.formatTime(new Date(data['end_time'] * 1000)));
        } else {
          msg = "财 年 已 结 束";
        }

        _this.setData({
          nowFinanceYear: msg
        });
      }
    })
  },

  compareTime: function(endDate) {
    let nowDate = utils.getNowDate();
    let _this = this;
    let min1 = parseInt(nowDate.substr(11, 2)) * 60 + parseInt(nowDate.substr(14, 2));
    let min2 = parseInt(endDate.substr(11, 2)) * 60 + parseInt(endDate.substr(14, 2));
    let n = min2 - min1;
    let h = 0;

    if (n > 60) {
      h = parseInt(n / 60);
      n = n % 60;
    }

    this.setData({
      remainTime: "剩余时间：" + h + " 时 " + n + " 分 "
    });

    this.timing = setInterval(function() {
      _this.compareTime(endDate)
    }, 30000);
  },

  onHide: function() {
    let _this = this;
    clearInterval(_this.timing);
  },
  onUnload: function() {
    let _this = this;
    clearInterval(_this.timing);
  },
  onShareAppMessage: function() {
    return {
      title: 'ITRClub-Wisecity6商赛小工具',
      path: '/subpackage/wisecity6/pages/index'
    }
  }
})