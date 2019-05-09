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

const formatBankType = type => {
  let ret = '';
  switch (type) {
    case "0":
      ret = '取款';
      break;
    case "1":
      ret = '存款';
      break;
    case "2":
      ret = '兑票';
      break;
    case "3":
      ret = '兑钱';
      break;
    case "4":
      ret = '借贷';
      break;
    case "5":
      ret = '注资';
      break;
    default:
      ret = '未知类型';
      break;
  }
  return ret;
}

module.exports = {
  formatTransactionType: formatTransactionType,
  formatBankType: formatBankType
}