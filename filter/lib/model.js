class Node {
  constructor() {
    this.name = '';
    this.children = [];
    this.attributes = [];
  }
}

class Loop {
  constructor(loopNodes) {
    this.name = 'Loop';
    this.children = loopNodes;
  }
}

module.exports = { Node, Loop };
