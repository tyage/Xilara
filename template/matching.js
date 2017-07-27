import { Node, Tag, Optional, Loop, Ignore } from './nodes'

class Nodes {
  constructor({ html, template }) {
    this.html = html
    this.template = template
  }
}

// optional states
const OPTIONAL_IS_EXISTS = Symbol()
const OPTIONAL_IS_NOT_EXISTS = Symbol()
class State {
  constructor({ rootNodes = null, nodes = null, optionalStates = new Map(), loopStates = new Map() }) {
    this.rootNodes = rootNodes
    this.nodes = nodes
    this.optionalStates = optionalStates
    this.loopStates = loopStates
    this.prevState = null
  }
}

const ROOT_NODE_FOUND = Symbol()
const findNextNode = (html, template, state) => {
  // if template is root, stop seek
  if (template === state.rootNodes.template) {
    if (html !== state.rootNodes.html) {
      throw new Error('all template checked but html remaining')
    }

    return ROOT_NODE_FOUND
  }

  // if no child found, check next element
  let nextHTML = html.next
  let nextTemplate = template.nextNode()

  // skip if optional is not exists
  while (state.optionalStates.get(nextTemplate) === OPTIONAL_IS_NOT_EXISTS) {
    nextTemplate = nextTemplate.nextNode()
  }

  // if template is Ignore, should see parent (ignore siblings)
  if (template instanceof Ignore) {
    nextHTML = null
    nextTemplate = null
  }

  if (nextTemplate !== null) {
    // if next element found, return it
    if (nextHTML === null) {
      if (!(nextTemplate instanceof Optional)) {
        throw new Error('no next html, but template has next')
      }
    }

    return {
      html: nextHTML,
      template: nextTemplate
    }
  } else {
    // if next element not found, search next node of parent
    if (nextHTML !== null) {
      // it may be broken
      // throw new Error('no next template, but html has next')
    }

    let templateParent = template.parent
    // skip parent Optional and Loop node
    while (templateParent instanceof Optional || templateParent instanceof Loop) {
      templateParent = templateParent.parent
    }
    return findNextNode(html.parent, templateParent, state)
  }
}

export const checkMatch = (htmlRoot, templateRoot) => {
  const rootNodes = new Nodes({ html: htmlRoot, template: templateRoot })
  let state = new State({
    rootNodes,
    nodes: rootNodes
  })

  while (true) {
    const {template, html} = state.nodes
    const htmlStr = html ? `<${html.name} ${Object.keys(html.attribs).join(' ')}>` : 'null'
    let nextState = null
    console.log(`html: ${htmlStr}, template: ${template}`)

    if (template instanceof Tag) {
      if (template.matchWith(html)) {
        // if html match with template, step next
        const htmlHasChild = html.children.length > 0
        const templateHasChild = template.children.length > 0
        if (templateHasChild) {
          // if template has child and html has no child, throw error
          // if template only has "Optional" or "Loop" child, it can be valid
          const templateHasOnlyOptionalOrLoopChild = template.children.length === 1 && (template.children[0] instanceof Optional || template.children[0] instanceof Loop)
          if (!htmlHasChild && !templateHasOnlyOptionalOrLoopChild) {
            throw new Error('template has child but html has no child')
          }

          // if template has children, check children
          nextState = {
            nodes: new Nodes({
              html: html.children[0],
              template: template.children[0]
            })
          }
        } else {
          if (htmlHasChild) {
            throw new Error('template has no child but html has child')
          }

          // if template has no children, find next node of html and template
          const result = findNextNode(html, template, state)
          if (result === ROOT_NODE_FOUND) {
            return true
          }
          nextState = {
            nodes: new Nodes({
              html: result.html,
              template: result.template
            })
          }
        }
      }
    } else if (template instanceof Optional) {
      if (!state.optionalStates.has(template)) {
        // set optional exists
        // TODO: key should be tree path
        state.optionalStates.set(template, OPTIONAL_IS_EXISTS)
        nextState = {
          nodes: new Nodes({
            html: html,
            template: template.children[0]
          })
        }
      } else {
        throw new Error('Optional template should be skipped')
      }
    } else if (template instanceof Loop) {
      if (!state.loopStates.has(template)) {
        // set loop count 1
        state.loopStates.set(template, {
          loopCount: 1,
          isLoopCountIncremented: true
        })
        nextState = {
          nodes: new Nodes({
            html: html,
            template: template.children[0]
          })
        }
      }
    } else if (template instanceof Ignore) {
      // ignore all nodes include siblings
      const result = findNextNode(html, template, state)
      nextState = {
        nodes: new Nodes({
          html: result.html,
          template: result.template
        })
      }
    } else {
      throw new Error('not implemented yet')
    }

    if (nextState === null) {
      // if next state not found, backtrack state
      console.log('backtracking start')
      while (true) {
        const {template, html} = state.nodes
        if (template instanceof Optional && state.optionalStates.get(template) === OPTIONAL_IS_EXISTS) {
          state = state.prevState
          state.optionalStates.set(template, OPTIONAL_IS_NOT_EXISTS)
          const result = findNextNode(state.nodes.html, state.nodes.template, state)
          if (result === ROOT_NODE_FOUND) {
            return true
          }
          nextState = {
            nodes: new Nodes({
              html: result.html,
              template: result.template
            })
          }

          break
        }

        // if backtracking is reached to root node, matching failed
        if (state.nodes === state.rootNodes) {
          return false
        }

        state = state.prevState
      }
    }

    // update to next state
    nextState.prevState = state
    state = Object.assign({}, state, nextState)
  }
}
