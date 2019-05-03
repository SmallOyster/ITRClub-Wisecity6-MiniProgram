const app = getApp();
var utils = require('../../../../utils/util.js');

Page({

  data: {
    StatusBar: app.globalData.StatusBar,
    CustomBar: app.globalData.CustomBar,
    loading: false,
    modalName: null,
    tipsContent: ''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    let _this = this;

    _this.setData({
      loading: true
    })

    wx.request({
      url: app.globalData.wisecityApiUrl + "team/treasury.php?mod=list&teamId=" + wx.getStorageSync('wisecity6_teamId'),
      dataType: 'json',
      success: function(ret) {
        ret = ret.data;
        _this.setData({
          loading: false
        });

        if (ret.code == 200) {
          let data = ret.data;
          let treasury = data['list'];

          if (treasury[3]) {
            treasury[0]['bank_name'] = '央行';
            treasury[0]['currency'] = '黄金';
            treasury[1]['bank_name'] = '央行';
            treasury[1]['currency'] = treasury[4]['currency'];
            treasury[2]['bank_name'] = '央行';
            treasury[2]['currency'] = treasury[5]['currency'];
            treasury[3]['currency'] = '黄金';
            treasury[6]['currency'] = '黄金';
            treasury[9]['currency'] = '黄金';
          }

          _this.setData({
            treasury: treasury
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
    } else if (opt.detail.value.moneyType >= 0) {
      wx.navigateTo({
        url: '../transLog/list?o=m&v=' + opt.detail.value.moneyType
      })
		} else if (opt.detail.value.wcNavToUrl && opt.detail.value.wcNavToUrl != '../treasury/index') {
      wx.redirectTo({
        url: opt.detail.value.wcNavToUrl,
      })
    }
  },
})