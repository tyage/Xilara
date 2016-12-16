class Node {
  constructor() {
    this.name = '';
    this.children = [];
    this.attributes = [];
  }
}

class Loop {
  constructor(loopNodes) {
    this.loopNodes = loopNodes;
  }
}

module.exports = { Node, Loop };
