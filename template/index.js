import { DomHandler, Parser } from 'htmlparser2'
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
  const state = {
    html: htmlRoot,
    template: templateRoot,
    prevState: []
  }

  while (true) {
    const htmlStr = state.html ? `<${state.html.name} ${Object.keys(state.html.attribs).join(' ')}>` : 'null'
    console.log(`html: ${htmlStr}, template: ${state.template}`)

    if (state.template instanceof Tag) {
      if (state.template.matchWith(state.html)) {
        // if match, step next
        const htmlHasChild = state.html.children.length > 0
        const templateHasChild = state.template.children.length > 0
        if (templateHasChild) {
          if (htmlHasChild) {
            // if has children, check children
            state.html = state.html.children[0]
            state.template = state.template.children[0]
            console.log('see children')
          } else {
            throw new Error('template has child but html has no child')
          }
        } else {
          if (htmlHasChild) {
            throw new Error('template has no child but html has child')
          } else {
            // if no child found, check next element
            const nextHTML = state.html.next
            const nextTemplate = state.template.nextNode()
            if (nextTemplate === null) {
              if (nextHTML === null) {
                // search next parent node
                let t = state.template.parent
                let h = state.html.parent
                while (true) {
                  if (t instanceof Optional) {
                    t = t.parent
                  }

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
                    // html may be empty (if template is optional)
                    state.template = t.nextNode()
                    state.html = h.next
                    console.log(`next node in parent ${state.template}`)
                    break
                  }
                }
              } else {
                throw new Error('no next template, but html has next')
              }
            } else {
              if (nextHTML === null) {
                throw new Error('no next html, but template has next')
              } else {
                state.html = nextHTML
                state.template = nextTemplate
                console.log('next siblings node')
              }
            }
          }
        }
      } else {
        throw new Error('backtrack not implemented yet')
      }
    } else if (state.template instanceof Optional) {
      // set optional exists
      state.template = state.template.children[0]
      console.log('optional node')
    } else {
      throw new Error('not implemented yet')
    }
  }
}

export const isHTMLMatchWithTemplate = (html, template) => {
  const parseHTML= new Promise((resolve, reject) => {
    const handler = new DomHandler((error, dom) => resolve(dom));
    handler.ontext = () => {}
    handler.oncomment = () => {}
    handler.oncommentend = () => {}
    const parser = new Parser(handler);
    parser.write(html);
    parser.done();
  })
  return parseHTML.then((dom) => {
    return new Promise((resolve, reject) => {
      // TODO: search html node
      resolve(checkMatch(dom[1], template))
    })
  })
}
