const isAllSame = (elements) => {
  return (new Set(elements)).size === 1;
};

module.exports = { isAllSame };
