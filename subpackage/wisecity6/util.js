const app = getApp();

const getwcNavInfo = () => {
  if (wx.getStorageSync('wisecity6_navInfo') == [] || wx.getStorageSync('wisecity6_navInfo') == undefined) {
    let ret = {};

    if (wx.getStorageSync('wisecity6_role') == 'group' || wx.getStorageSync('wisecity6_role') == 'admin') {
      ret = {
        navRole: 'top',
        navUrl: 'my',
        navIcon: 'profile',
        navName: '我'
      }
    } else if (wx.getStorageSync('wisecity6_role') != '') {
      ret = {
        navRole: 'teamRed',
        navUrl: 'transLog',
        navIcon: 'cart',
        navName: '交易'
      }
    } else {
      return;
    }

    wx.setStorageSync('wisecity6_navInfo', ret);
    return ret;
  } else {
    return wx.getStorageSync('wisecity6_navInfo');
  }
}

module.exports = {
  getwcNavInfo: getwcNavInfo
}