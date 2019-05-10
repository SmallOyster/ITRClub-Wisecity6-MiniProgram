var utils = require('../../utils/util.js');
const app = getApp();

Page({
	data: {
		StatusBar: app.globalData.StatusBar,
		CustomBar: app.globalData.CustomBar,
  },

  onLoad: function(options) {

  },

  onShow: function() {
    var _this = this;

    _this.setData({
      toggleDelay: true
    })
    setTimeout(function() {
      _this.setData({
        toggleDelay: false
      })
    }, 1000)
  },

	formSubmit: function (opt) {
		utils.collectFormId(opt.detail.formId);
	},

	onShareAppMessage: function () {
		return {
			title: '[数字ITRClub]小程序功能列表',
			path: '/pages/function/index'
		}
	},
})
