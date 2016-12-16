const ElementType = require('domelementtype');
const { Node } = require('../lib/model');

const truncateGarbage = (html) => {
  const newHTML = [];

  html.forEach(e => {
    if (!ElementType.isTag(e)) {
      return;
    }

    const node = new Node();
    node.name = e.name;
    node.children = truncateGarbage(e.children);
    node.attributes = e.attribs;
    newHTML.push(node);
  });
  return newHTML;
};

module.exports = { truncateGarbage };
