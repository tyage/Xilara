import { parseString } from 'xml2js'
import { Node, Tag, Optional } from './nodes'

export const stringifyTemplate = (template, indent = 0) => {
  const elem = ' '.repeat(indent) + '- ' + template + '\n'

  let children = ''
  if (template.children.length > 0) {
    children = template.children.map(e => stringifyTemplate(e, indent + 1)).join('')
  }

  return elem + children
}

const checkMatch = (htmlRoot, templateRoot) => {
  const getNextHTML = (html) => {
    if (html.list.length <= html.index) {
      return null
    }
    return {
      list: html.list,
      index: html.index + 1,
      parent: html.parent
    }
  }
  const getCurrentHTMLNode = (html) => {
    return html.list[html.index]
  }

  const state = {
    html: {
      list: [ htmlRoot ],
      index: 0,
      parent: null
    },
    template: templateRoot
  }

  while (true) {
    let htmlNode = getCurrentHTMLNode(state.html)
    console.log(`html: ${htmlNode['#name']}, template: ${state.template}`)

    if (state.template instanceof Tag) {
      if (state.template.matchWith(htmlNode)) {
        // if match, step next
        const htmlHasChild = htmlNode.$$ && htmlNode.$$.length > 0
        const templateHasChild = state.template.children.length > 0
        if (templateHasChild) {
          if (htmlHasChild) {
            // if has children, check children
            console.log(`childlength: ${htmlNode.$$.length}`)
            state.html = {
              list: htmlNode.$$,
              index: 0,
              parent: state.html
            }
            state.template = state.template.children[0]
          } else {
            throw new Error('template has child but html has no child')
          }
        } else {
          if (htmlHasChild) {
            console.log(state.html)
            throw new Error('template has no child but html has child')
          } else {
            // if no child found, check next element
            const nextHTML = getNextHTML(state.html)
            const nextTemplate = state.template.nextNode()
            if (nextTemplate === null) {
              if (nextHTML === null) {
                // search next parent node
                let t = state.tempate.parent
                let h = state.html.parent
                while (true) {
                  if (t === templateRoot) {
                    if (h === htmlRoot) {
                      return true
                    } else {
                      throw new Error('all template checked but html remaining')
                    }
                  }

                  if (t.nextNode() === null) {
                    // TODO: check html
                    t = t.parent
                    h = h.parent
                  } else {
                    // next template found!
                    // TODO: check html
                    state.template = t.nextNode()
                    state.html = getNextHTML(h)
                    break
                  }
                }
              } else {
                throw new Error('no next template, but html has next')
              }
            } else {
              state.html = nextHTML
              state.template = nextTemplate
            }
          }
        }
      } else {
        throw new Error('backtrack not implemented yet')
      }
    } else {
      throw new Error('not implemented yet')
    }
  }
}

export const isHTMLMatchWithTemplate = (html, template) => {
  return new Promise((resolve, reject) => {
    parseString(html, {
      explicitChildren: true,
      preserveChildrenOrder: true,
      strict: false
    }, (err, result) => {
      resolve(checkMatch(result.HTML, template))
    })
  })
}
