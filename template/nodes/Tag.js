import Node from './Node'

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
    if (!html) {
      return false
    }

    // check tag name
    const nameMatch = html.name.toLowerCase() === this.name.toLowerCase()
    if (!nameMatch) {
      return false
    }

    // check attrs
    // make attribute name lower case
    const originalHTMLAttrs = html.attribs
    const htmlAttrs = {}
    Object.keys(originalHTMLAttrs).forEach(k => {
      htmlAttrs[k.toLowerCase()] = originalHTMLAttrs[k]
    })
    const notMatchedHTMLAttrs = Object.keys(htmlAttrs)

    for (const [k, v] of this.attrs.entries()) {
      const name = k.toLowerCase()
      if (v !== undefined && htmlAttrs[name] !== v) {
        // XXX: dont check value now, just warning (for multiple attributes problem)
        console.warn(`attr ${name} is not same: ${htmlAttrs[name]}, ${v}`)
        // return false
      }

      const index = notMatchedHTMLAttrs.indexOf(name)
      notMatchedHTMLAttrs.splice(index, 1)
    }

    return notMatchedHTMLAttrs.length === 0
  }
}
