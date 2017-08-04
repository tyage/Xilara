import Node from './Node'

export default class Ignore extends Node {
  constructor(elem) {
    super()

    this.elem = elem
  }
  toString() {
    return '<Ignore>'
  }
}
