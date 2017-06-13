export default class Node {
  constructor() {
    this.position = 0
    this.children = []
    this.parent = null
  }
  matchWith() {
    throw new Error('not implemented yet')
  }
  addChild(child) {
    if (child === null) {
      return
    }

    child.position = this.children.length
    this.children.push(child)
    child.parent = this
  }
  addChildren(children) {
    children.forEach(child => this.addChild(child))
  }
  nextNode() {
    if (this.parent.children.length - 1 <= this.positon) {
      return null
    }
    return this.parent.children[this.position + 1]
  }
  prevNode() {
    if (this.position <= 0) {
      return null
    }
    return this.parent.children[this.position - 1]
  }
}
