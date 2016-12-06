const ElementType = require('domelementtype');

/*
<ul>
  <li>1
  <li>2
</ul>
=>
- ul
  - [loop]
    - li
    - text
*/

class Node {
  constructor() {
    this.name = '';
    this.children = [];
    this.attributes = [];
  }
}

class Loop {
}

const isAllSame = (elements) => {
  return (new Set(elements)).size === 1;
};

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

const createModel = (htmls) => {
  const model = [];

  htmls = htmls.map(html => truncateGarbage(html));
  if (htmls.length === 0) {
    return model;
  }

  return createChildModel(htmls);
};

const createChildModel = (htmls) => {
  const model = [];

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

      // TODO: check attributes?

      // create child model
      const childrenList = nodes.map(node => node.children);
      const childrenModel = createChildModel(childrenList);

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
