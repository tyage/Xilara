const { Node } = require('../lib/model');
const { isAllSame } = require('../lib/helper');

// Create a model only with tag name
const createModel = (htmls) => {
  let model = [];

  if (htmls.length === 0) {
    return model;
  }

  // check if child length is same
  const lengths = htmls.map(html => html.length);
  if (isAllSame(lengths)) {
    const length = lengths[0];
    // then, check if all elements are the same
    for (let i = 0; i < length; ++i) {
      const nodes = htmls.map(html => html[i]);

      // check name
      const names = nodes.map(node => node.name);
      const isAllTagsSame = isAllSame(names);
      if (!isAllTagsSame) {
        throw new Error(`tags are differ! ${nodes}`);
      }

      // create child model
      const childrenList = nodes.map(node => node.children);
      const childrenModel = createModel(childrenList);

      // add node
      const node = new Node();
      node.name = names[0];
      node.children = childrenModel;
      model.push(node);
    }
  } else {
    // size is differ!
    throw new Error(`tags are differ! ${JSON.stringify(htmls)}`);
  }

  return model;
};

module.exports = { createModel };
