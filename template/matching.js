import { Node, Tag, Optional, Loop } from './nodes'

class Nodes {
  constructor({ html, template, prevNodes = null }) {
    this.html = html
    this.template = template
    this.prevNodes = prevNodes
  }
}

class State {
  constructor({ rootNodes = null, nodes = null, optionalStates = new WeakMap() }) {
    this.rootNodes = rootNodes
    this.nodes = nodes
    this.optionalStates = optionalStates
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
const findNextNode = (html, template, state, ignoreOption) => {
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
  while (nextTemplate === ignoreOption) {
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
    while (templateParent instanceof Optional) {
      templateParent = templateParent.parent
    }
    return findNextNode(html.parent, templateParent, state, ignoreOption)
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
          if (!htmlHasChild) {
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
          const result = findNextNode(state.nodes.html, state.nodes.template, state, undefined)
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
          if (state.nodes.template instanceof Optional && state.nodes.optionalState) {
            const ignoreOption = state.nodes.template
            state.backtrackNodes()

            const result = findNextNode(state.nodes.html, state.nodes.template, state, ignoreOption)
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
      if (state.nodes.optionalState === undefined) {
        // set optional exists
        state.nodes.optionalState = true
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
