import Node from './node'

export default class Tag extends Node {
  constructor(element, attrs, closing = false) {
    super()

    this.element = element
    this.attrs = attrs
    this.closing = closing
  }
  toString() {
    if (this.closing) {
      return `</${this.element}>`
    } else {
      const attrs = Array.from(this.attrs.entries()).map(([k, v]) => {
        if (v) {
          return `${k}="${v}"`
        } else {
          return `${k}`
        }
      }).join(' ')

      return `<${this.element} ${attrs}>`
    }
  }
  matchWith(html) {
    return html['#name'].toLowerCase() === this.element.toLowerCase()
  }
}
