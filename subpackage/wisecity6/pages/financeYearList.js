const app = getApp();
var utils = require('../../../utils/util.js');
var wcUtils = require('../util.js');

Page({

  data: {
    StatusBar: app.globalData.StatusBar,
    CustomBar: app.globalData.CustomBar,
    wcNavData: wcUtils.getwcNavInfo(),
    loading: false
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    let _this = this;

    this.setData({
      loading: true
    })

    wx.request({
      url: app.globalData.wisecityApiUrl + "financeYear.php?mod=list",
      dataType: 'json',
      success: function(ret) {
        ret = ret.data;
        _this.setData({
          loading: false
        });

        if (ret.code == 200) {
          let data = ret.data;
          let financeYearList = data['list'];

          for (let i in financeYearList) {
            financeYearList[i]['start_time'] = utils.formatTime(new Date(financeYearList[i]['start_time'] * 1000));
            financeYearList[i]['end_time'] = utils.formatTime(new Date(financeYearList[i]['end_time'] * 1000));
          }

          _this.setData({
            financeYearList: financeYearList
          })
        }
      }
    })
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {
    return {
      title: 'ITRClub-Wisecity6商赛财年列表',
      path: '/subpackage/wisecity6/pages/financeYearList'
    }
  },

  collectFID: function(opt) {
    utils.collectFormId(opt.detail.formId);

		if (opt.detail.value.wcNavToUrl && opt.detail.value.wcNavToUrl != 'financeYearList') {
      wx.redirectTo({
        url: opt.detail.value.wcNavToUrl,
      })
    }
  },
})