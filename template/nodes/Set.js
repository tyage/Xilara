import Node from './node'

export default class Set extends Node {
  constructor() {
    super()
  }
  toString() {
    return '<Set>'
  }
  matchWith(html) {
    return this.children[0].matchWith(html[0])
  }
}
