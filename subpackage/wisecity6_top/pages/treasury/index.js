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
      url: app.globalData.wisecityApiUrl + "group/treasury.php?mod=list&groupId=" + wx.getStorageSync('wisecity6_groupId'),
      dataType: 'json',
      success: function(ret) {
        ret = ret.data;
        _this.setData({
          loading: false
        });

        if (ret.code == 200) {
          let data = ret.data;
          let treasury = data['list'];

          _this.setData({
            treasury: treasury
          })
        }
      }
    })
  },

	collectFID: function (opt) {
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