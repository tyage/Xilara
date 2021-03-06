import Node from './Node'

export default class Tag extends Node {
  constructor(name, attrs = new Map) {
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
      if (!this.attrMatchWith(k, htmlAttrs[name])) {
        console.log(`attr ${name} does not match ${JSON.stringify(htmlAttrs[name])} ${JSON.stringify(v)}`)
        return false
      }

      const index = notMatchedHTMLAttrs.indexOf(name)
      notMatchedHTMLAttrs.splice(index, 1)
    }

    return notMatchedHTMLAttrs.length === 0
  }
  attrMatchWith(name, htmlAttrValue) {
    const shouldCheckValue = ['id', 'class'].includes(name)
    let templateAttrValue = this.attrs.get(name)

    if (templateAttrValue === undefined) {
      return true;
    }
    if (!(templateAttrValue instanceof Array)) {
      templateAttrValue = [templateAttrValue];
    }
    if (templateAttrValue.length === 0) {
      console.warn('template attr must have 1 or more values')
      return false
    }
    // if there is no html attr, return false
    if (htmlAttrValue === undefined) {
      return false
    }

    // if template has no "javascript:..." and html has, it is invalid
    if (name.toLowerCase() === 'href') {
      let templateAttrHasJavaScriptContext = false
      for (let value of templateAttrValue) {
        if (value.toLowerCase().startsWith('javascript:')) {
          templateAttrHasJavaScriptContext = true
          break
        }
      }
      const htmlAttrHasJavaScriptContext = htmlAttrValue.toLowerCase().startsWith('javascript:')
      if (!templateAttrHasJavaScriptContext && htmlAttrHasJavaScriptContext) {
        return false
      }
    }

    // check value of only id, class attributes
    // if template has fixed value
    if (shouldCheckValue) {
      let attrHasUniqueValue = true
      for (let value of templateAttrValue) {
        if (value !== templateAttrValue[0]) {
          attrHasUniqueValue = false
          break
        }
      }
      if (attrHasUniqueValue && htmlAttrValue !== templateAttrValue[0]) {
        return false
      }
    }

    return true;
  }
}
