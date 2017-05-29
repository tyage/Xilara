import Node from './node'

export default class Text extends Node {
  constructor(text) {
    super()

    this.text = text
  }
  toString() {
    return `"${this.text}"`
  }
}
