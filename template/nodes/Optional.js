import Node from './node'

export default class Optional extends Node {
  constructor(elem) {
    super()

    this.elem = elem
  }
  toString() {
    return '<Optional>'
  }
}
