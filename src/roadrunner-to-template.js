import fs from 'fs'
import { parseString } from 'xml2js'
import { Node, Tag, Optional, Loop, Ignore } from './nodes'

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
      return roadrunnerToTemplate(elem.$$[0])
    case 'pcdata':
      return null
    case 'and':
      // return list of child elements
      const childElements = elem.$$
      const rootNode = new Node()
      let currentNode = rootNode
      for (let i = 0; i < childElements.length; ++i) {
        const childElement = childElements[i]
        const childNode = roadrunnerToTemplate(childElement)
        if (childNode instanceof Tag) {
          if (childNode.name[0] === '/') {
            currentNode = currentNode.parent || rootNode
          } else {
            currentNode.addChild(childNode)
            currentNode = childNode
          }
        } else {
          currentNode.addChild(childNode)
        }
      }
      return rootNode.children
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
    case 'subtree':
      return new Ignore()
    default:
      return null
  }

  if (elem.$$) {
    node.addChildren(roadrunnerListToTemplateList(elem.$$))
  }

  return node
}
export const roadrunnerListToTemplateList = (list) => {
  const templateList = []
  list.forEach(elem => {
    const template = roadrunnerToTemplate(elem)
    if (template === null) {
      return
    }

    // roadrunner may have set of elements as a element
    if (Array.isArray(template)) {
      template.forEach(t => templateList.push(t))
    } else {
      templateList.push(template)
    }
  })
  return templateList
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
