import fs from 'fs'
import { parseString } from 'xml2js'
import { Node, Set, Tag, Text, Variant, Optional } from '../template/nodes'

export const roadrunnerToTemplate = (expression) => {
  return expression.map(elem => {
    const name = elem['#name']
    let node = new Node()
    switch (name) {
      case 'hook':
        node = new Optional()
        break
      case 'plus':
        node = new Loop()
        break
      case 'variant':
        // 不要説ある
        node = new Variant()
        break
      case 'pcdata':
        node = new Text(elem._)
        break
      case 'and':
        node = new Set()
        break
      case 'tag':
        const { element, depth, attrs } = elem.$

        let elementName = element

        const attrMap = new Map
        attrs.split(',').forEach(attr => {
          const [key, value]  = attr.split(':')
          attrMap.set(key, value)
        })

        const closing = element[0] === '/'
        if (closing) {
          elementName = elementName.slice(1)
        }

        node = new Tag(elementName, attrMap, closing)
        break
    }

    if (elem.$$) {
      node.children = roadrunnerToTemplate(elem.$$)
    }

    return node
  })
}

export const roadrunnerFileToTemplate = (roadrunnerXMLFile) => {
  return new Promise((resolve, reject) => {
    const buf = fs.readFileSync(roadrunnerXMLFile)
    parseString(buf.toString(), {
      explicitChildren: true,
      preserveChildrenOrder: true
    }, (err, result) => {
      if (err) {
        reject(err)
      }

      const template = roadrunnerToTemplate(result.wrapper.expression[0].$$)
      resolve(resolve)
    })
  })
}
