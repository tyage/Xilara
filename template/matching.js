import { Node, Tag, Optional, Loop } from './nodes'

class Nodes {
  constructor({ html, template, prevNodes = null }) {
    this.html = html
    this.template = template
    this.prevNodes = prevNodes
  }
}

const OPTIONAL_IS_EXISTS = Symbol()
const OPTIONAL_IS_NOT_EXISTS = Symbol()
class State {
  constructor({ rootNodes = null, nodes = null, optionalStates = new WeakMap(), loopCounts = new WeakMap() }) {
    this.rootNodes = rootNodes
    this.nodes = nodes
    this.optionalStates = optionalStates
    this.loopCounts = loopCounts
  }

  updateNodes(nextNodes) {
    nextNodes.prevNodes = this.nodes
    this.nodes = nextNodes
  }

  backtrackNodes() {
    this.nodes = this.nodes.prevNodes
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
  const nextHTML = html.next
  let nextTemplate = template.nextNode()
  while (state.optionalStates.get(nextTemplate) === OPTIONAL_IS_NOT_EXISTS) {
    nextTemplate = nextTemplate.nextNode()
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
    const htmlStr = state.nodes.html ? `<${state.nodes.html.name} ${Object.keys(state.nodes.html.attribs).join(' ')}>` : 'null'
    //console.log(`html: ${htmlStr}, template: ${state.nodes.template}`)

    if (state.nodes.template instanceof Tag) {
      if (state.nodes.template.matchWith(state.nodes.html)) {
        // if html match with template, step next
        const htmlHasChild = state.nodes.html.children.length > 0
        const templateHasChild = state.nodes.template.children.length > 0
        if (templateHasChild) {
          // if template has child and html has no child, throw error
          // if template only has "Optional" child, it can be valid
          const templateHasOnlyOptionalOrLoopChild = state.nodes.template.children.length === 1 && (state.nodes.template.children[0] instanceof Optional || state.nodes.template.children[0] instanceof Loop)
          if (!htmlHasChild && !templateHasOnlyOptionalOrLoopChild) {
            throw new Error('template has child but html has no child')
          }

          // if template has children, check children
          state.updateNodes(new Nodes({
            html: state.nodes.html.children[0],
            template: state.nodes.template.children[0]
          }))
        } else {
          if (htmlHasChild) {
            throw new Error('template has no child but html has child')
          }

          // if template has no children, find next node of html and template
          const result = findNextNode(state.nodes.html, state.nodes.template, state)
          if (result === ROOT_NODE_FOUND) {
            return true
          }
          state.updateNodes(new Nodes({
            html: result.html,
            template: result.template
          }))
        }
      } else {
        // if html not match, back to previous state
        while (true) {
          if (state.nodes.template instanceof Optional && state.optionalStates.get(state.nodes.template) === OPTIONAL_IS_EXISTS) {
            state.optionalStates.set(state.nodes.template, OPTIONAL_IS_NOT_EXISTS)
            state.backtrackNodes()

            const result = findNextNode(state.nodes.html, state.nodes.template, state)
            if (result === ROOT_NODE_FOUND) {
              return true
            }
            state.updateNodes(new Nodes({
              html: result.html,
              template: result.template
            }))

            break
          }

          // if backtracking is reached to root node, matching failed
          if (state.nodes === state.rootNodes) {
            return false
          }

          state.backtrackNodes()
        }
      }
    } else if (state.nodes.template instanceof Optional) {
      if (!state.optionalStates.has(state.nodes.template)) {
        // set optional exists
        state.optionalStates.set(state.nodes.template, OPTIONAL_IS_EXISTS)
        state.updateNodes(new Nodes({
          html: state.nodes.html,
          template: state.nodes.template.children[0],
        }))
        //console.log('optional node')
      }
    } else {
      throw new Error('not implemented yet')
    }
  }
}
