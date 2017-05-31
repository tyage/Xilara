import Node from './node'

export default class Tag extends Node {
  constructor(name, attrs) {
    super()

    this.name = name
    this.attrs = attrs
  }
  toString() {
    const attrs = Array.from(this.attrs.entries()).map(([k, v]) => {
      if (v) {
        return `${k}="${v}"`
      } else {
        return `${k}`
      }
    }).join(' ')

    return `<${this.name} ${attrs}>`
  }
  matchWith(html) {
    const nameMatch = html['#name'].toLowerCase() === this.name.toLowerCase()
    if (!nameMatch) {
      return false
    }

    // make attribute name lower case
    const originalHTMLAttrs = html.$ || {}
    const htmlAttrs = {}
    Object.keys(originalHTMLAttrs).forEach(k => {
      htmlAttrs[k.toLowerCase()] = originalHTMLAttrs[k]
    })
    const notMatchedHTMLAttrs = Object.keys(htmlAttrs)

    for (const [k, v] of this.attrs.entries()) {
      const name = k.toLowerCase()
      if (htmlAttrs[name] !== v) {
        return false
      }

      const index = notMatchedHTMLAttrs.indexOf(name)
      notMatchedHTMLAttrs.splice(index, 1)
    }
    return notMatchedHTMLAttrs.length === 0
  }
}
