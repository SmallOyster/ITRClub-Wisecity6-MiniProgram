//app.js
// 开工时间：2019-02-14

App({
  globalData: {
    baseUrl: "https://ssouc.itrclub.com/",
    apiUrl: "https://ssouc.itrclub.com/api/wxmp/",
    wisecityApiUrl: "https://wx.itrclub.com/wisecity6/",
    wisecityApiUrl2: "https://wisecity.itrclub.com/api/"
  },
  onLaunch: function() {
    // 获取系统状态栏信息
    wx.getSystemInfo({
      success: e => {
        this.globalData.StatusBar = e.statusBarHeight;
        this.globalData.CustomBar = e.platform == 'android' ? e.statusBarHeight + 50 : e.statusBarHeight + 45;
      }
    })
  }
})