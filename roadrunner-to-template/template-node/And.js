import Node from './node'

export default class And extends Node {
  constructor(elem) {
    super()

    this.elem = elem
  }
  toString() {
    return JSON.stringify(this.elem)
  }
}
