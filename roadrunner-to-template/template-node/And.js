import Node from './node'

class And extends Node {
  constructor(elem) {
    super()

    this.elem = elem
  }
  toString() {
    return JSON.stringify(this.elem)
  }
}

export default And
