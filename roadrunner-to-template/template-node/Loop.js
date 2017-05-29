import Node from './node'

export default class Loop extends Node {
  constructor(elem) {
    super()

    this.elem = elem
  }
  toString() {
    return '<Loop>'
  }
}
