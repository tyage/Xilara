import Node from './node'

export default class Variant extends Node {
  constructor(elem) {
    super()

    this.elem = elem
  }
  toString() {
    return JSON.stringify(this.elem)
  }
}
