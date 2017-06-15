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
  let state = {
    html: htmlRoot,
    template: templateRoot,
    prevState: null
  }
  const setNextState = (nextState) => {
    state = Object.assign({}, state, {
      prevState: state
    }, nextState)
  }
  const ROOT_NODE_FOUND = Symbol()
  const findNextNode = (html, template, ignoreOption) => {
    // if template is root, stop seek
    if (template.parent === null) {
      if (html.parent !== null) {
        throw new Error('all template checked but html remaining')
      }

      return ROOT_NODE_FOUND
    }

    // if no child found, check next element
    const nextHTML = html.next
    let nextTemplate = template.nextNode()
    while (nextTemplate === ignoreOption) {
      nextTemplate = nextTemplate.nextNode()
    }

    if (nextTemplate !== null) {
      // if next element found, return it
      if (nextHTML === null) {
        throw new Error('no next html, but template has next')
      }

      return {
        html: nextHTML,
        template: nextTemplate
      }
    } else {
      // if next element not found, search next node of parent
      if (nextHTML !== null) {
        throw new Error('no next template, but html has next')
      }

      let templateParent = template.parent
      while (templateParent instanceof Optional) {
        templateParent = templateParent.parent
      }
      return findNextNode(html.parent, templateParent, ignoreOption)
    }
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
          if (!htmlHasChild) {
            throw new Error('template has child but html has no child')
          }

          // if has children, check children
          setNextState({
            html: state.html.children[0],
            template: state.template.children[0]
          })
        } else {
          if (htmlHasChild) {
            throw new Error('template has no child but html has child')
          }

          const result = findNextNode(state.html, state.template, undefined)
          if (result === ROOT_NODE_FOUND) {
            return true
          }
          setNextState({
            html: result.html,
            template: result.template
          })
        }
      } else {
        while (true) {
          if (state.template instanceof Optional && state.optionalState) {
            const ignoreOption = state.template
            state = state.prevState

            const result = findNextNode(state.html, state.template, ignoreOption)
            if (result === ROOT_NODE_FOUND) {
              return true
            }
            setNextState({
              html: result.html,
              template: result.template
            })

            break
          }

          state = state.prevState
          if (state === null) {
            return false
          }
        }
      }
    } else if (state.template instanceof Optional) {
      if (state.optionalState === undefined) {
        // set optional exists
        state.optionalState = true
        setNextState({
          template: state.template.children[0],
        })
        //console.log('optional node')
      }
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
