const formatTransactionType = type => {
  let ret = '';
  switch (type) {
    case "0":
      ret = '卖出';
      break;
    case "1":
      ret = '买入';
      break;
    default:
      ret = '未知类型';
      break;
  }
  return ret;
}

module.exports = {
  formatTransactionType: formatTransactionType
}