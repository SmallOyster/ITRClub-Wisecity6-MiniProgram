const app = getApp();
var utils = require('../../../utils/util.js');

Page({

  data: {
    StatusBar: app.globalData.StatusBar,
    CustomBar: app.globalData.CustomBar,
    showUserName: 'none',
    showPassword: 'none',
    modalName: null,
    tipsContent: '',
    bindSuccess: false
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {

  },

  showUserName: function(obj) {
    utils.collectFormId(obj.detail.formId);
    this.setData({
      showUserName: ''
    });
  },

  showPassword: function(obj) {
    utils.collectFormId(obj.detail.formId);
    this.setData({
      showPassword: ''
    });
  },

  toBindUser: function(obj) {
    var _this = this;
    var formData = obj.detail.value;
    var formId = obj.detail.formId;
    utils.collectFormId(formId);

    if (this.data.showUserName != '' || this.data.showPassword != '') {
      wx.showModal({
        title: '温馨提示',
        content: '请点击后输入用户名密码',
        showCancel: false
      })
      return false;
    }

    this.setData({
      loading: true
    });

    wx.request({
      url: app.globalData.wisecityApiUrl + "bindUser.php",
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      data: {
        'mod': 'bind',
        'openId': wx.getStorageSync('openId'),
        'userName': formData.userName,
        'password': formData.password
      },
      method: 'post',
      dataType: 'json',
      success: function(ret) {
        ret = ret.data;
        _this.setData({
          loading: false
        });

        if (ret.code == 200) {
          let data = ret.data;
          wx.setStorageSync('wisecity6_role', data['role']);

          if (data['role'] == 'group') {
            wx.setStorageSync('wisecity6_realName', data['realName']);
            wx.setStorageSync('wisecity6_groupId', data['groupId']);
            wx.setStorageSync('wisecity6_groupName', data['groupName']);
          } else if (data['role'] == 'team' || data['role'] == 'red') {
            wx.setStorageSync('wisecity6_teamId', data['teamId']);
            wx.setStorageSync('wisecity6_teamName', data['teamName']);
            wx.setStorageSync('wisecity6_groupName', data['groupName']);
          } else if (data['role'] == 'admin') {
            wx.setStorageSync('wisecity6_realName', data['realName']);
          }

          _this.setData({
            modalName: 'tipsModal',
            tipsContent: '绑定成功！',
            bindSuccess: true
          })
        } else if (ret.code == 403) {
          _this.setData({
            modalName: 'tipsModal',
            tipsContent: '用户名或密码错误！'
          })
        } else {
          _this.setData({
            modalName: 'tipsModal',
            tipsContent: '绑定失败！请联系技术支持并提交错误码：' + ret.code + "-" + ret.message
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
    }

    if (this.data.bindSuccess === true) {
      wx.switchTab({
        url: '/pages/function/index',
      })
    }
  },
})