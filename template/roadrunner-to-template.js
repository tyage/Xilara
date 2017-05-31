import fs from 'fs'
import { parseString } from 'xml2js'
import { Node, Set, Tag, Text, Variant, Optional } from './nodes'

export const roadrunnerToTemplate = (elem) => {
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
      const childElements = elem.$$
      node = new Set()
      let currentNode = node
      for (let i = 0; i < childElements.length; ++i) {
        const childElement = childElements[i]
        const childNode = roadrunnerToTemplate(childElement)
        if (childNode instanceof Tag) {
          if (childNode.name[0] === '/') {
            currentNode = currentNode.parent || node
          } else {
            currentNode.addChild(childNode)
            currentNode = childNode
          }
        } else {
          currentNode.addChild(childNode)
        }
      }
      break
    case 'tag':
      const { element, depth, attrs } = elem.$

      const attrMap = new Map
      attrs.split(',').forEach(attr => {
        const [key, value]  = attr.split(':')
        attrMap.set(key, value)
      })

      node = new Tag(element, attrMap)
      break
  }

  if (elem.$$ && name !== 'and') {
    node.addChildren(roadrunnerListToTemplateList(elem.$$))
  }

  return node
}
export const roadrunnerListToTemplateList = (list) => {
  return list.map(elem => roadrunnerToTemplate(elem))
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

      const template = roadrunnerListToTemplateList(result.wrapper.expression[0].$$)
      resolve(template[0])
    })
  })
}
