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
    const templateAttrValue = this.attrs.get(name)
    // if template attr has candidates of its value, templateAttrValue is Array
    if (templateAttrValue instanceof Array) {
      // if there is no html attr, return false
      if (!htmlAttrValue) {
        return false
      }

      if (templateAttrValue.length === 0) {
        console.warn('template attr must have 1 or more values')
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

      // if attr has multiple values, do not check value
      // if not, check value is matched
      let attrHasUniqueValue = true
      for (let value of templateAttrValue) {
        if (value !== templateAttrValue[0]) {
          attrHasUniqueValue = false
          break
        }
      }

      if (attrHasUniqueValue) {
        return htmlAttrValue === templateAttrValue[0]
      } else {
        return true
      }
    } else {
      return templateAttrValue === undefined || templateAttrValue === htmlAttrValue
    }
  }
}
