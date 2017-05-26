import { Node, And, Tag } from './template-node'

export const roadRunnerToTemplate = (expression) => {
  return expression.map(elem => {
    const name = elem['#name']
    let node = new Node()
    switch (name) {
      case 'and':
        node = new And(elem)
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
      node.children = roadRunnerToTemplate(elem.$$)
    }

    return node
  })
}
