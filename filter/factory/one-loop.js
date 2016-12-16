/*
<ul>
  <li>1
  <li>2
</ul>
=>
- ul
  - [loop]
    - li
*/

const { Node, Loop } = require('../lib/model');
const { isAllSame } = require('../lib/helper');

// [ Node ] => Node
const createNodeModel = (nodes) => {
  // check name
  const names = nodes.map(node => node.name);
  const isAllTagsSame = isAllSame(names);
  if (!isAllTagsSame) {
    throw new Error(`nodes are differ! ${nodes}`);
  }

  // create child model
  const childrenList = nodes.map(node => node.children);
  const childrenModel = createModel(childrenList);

  // add node
  const node = new Node();
  node.name = names[0];
  node.children = childrenModel;
  return node;
};

// CDCDE, CD => { count: 2, tail: E }
const deleteLoop = (html, model) => {
  const htmlLength = html.length;
  const modelLength = model.length;

  let matchLength = 0;
  for (; matchLength < htmlLength; ++matchLength) {
    const node = html[matchLength];
    const modelNode = model[matchLength % modelLength];
    if (!areNodesSame(node, modelNode)) {
      break;
    }
  }
  const matchCount = parseInt(matchLength / modelLength);

  return {
    count: matchCount,
    tail: html.slice(matchCount * modelLength)
  };
};

const areNodesSame = (...nodes) => {
  if (nodes.length === 0) {
    throw new Error(`nodes should have at least one node!`);
  }

  const names = nodes.map(node => node.name);
  if (!isAllSame(names)) {
    return false;
  }

  const childrenLength = nodes.map(node => node.children.length);
  if (!isAllSame(childrenLength)) {
    return false;
  }

  for (let i = 0; i < childrenLength[0]; ++i) {
    const childrenList = nodes.map(node => node.children[i]);
    if (!areNodesSame(...childrenList)) {
      return false;
    }
  }

  return true;
}

// [ html ] => html
// html = [ Node ]
// DP使えそうな気がする
const createModel = (htmls) => {
  if (htmls.length === 0) {
    return [];
  }

  // check head to tail sequentially
  let model = [];
  const lengths = htmls.map(html => html.length);
  const minLength = Math.min(...lengths);
  for (let index = 0; index < minLength; ++index) {
    const nodes = htmls.map(html => html[index]);
    const nodeModel = createNodeModel(nodes);
    const newModel = [...model, nodeModel];

    // ABCD => [ (ABCD)+, A(BCD)+, AB(CD)+, ABC(D)+ ]
    for (let loopIndex = 0; loopIndex < newModel.length; ++loopIndex) {
      const headModel = newModel.slice(0, loopIndex);
      const loopNodes = newModel.slice(loopIndex);

      // create model with tails
      const matches = htmls.map(html => deleteLoop(html.slice(index), loopNodes));
      const tails = matches.map(({ count, tail }) => tail);
      let tailModel = [];
      try {
        tailModel = createModel(tails);
      } catch(e) {
        continue;
      }

      // loop found!
      return [...headModel, new Loop(loopNodes), ...tailModel];
    }

    // loop not found
    model = newModel;
  }

  // if there are still existing nodes
  if (!isAllSame(lengths)) {
    throw new Error(`htmls are differ! ${htmls}`);
  }

  return model;
};

module.exports = { createModel };
