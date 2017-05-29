import Node from './node'

export default class Set extends Node {
  constructor(elem) {
    super()

    this.elem = elem
  }
  toString() {
    return '<Set>'
  }
}
