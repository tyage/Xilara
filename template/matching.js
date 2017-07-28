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

  // if template is Loop
  if (template instanceof Loop) {
    if (nextTemplate === null) {
      if (nextHTML === null) {
        // no next template, no next html
        return findNextNode(html.parent, template.parent)
      } else {
        if (state.loopStates.get(template).isLoopCountIncremented) {
          return {
            html: nextHTML,
            template: template
          }
        } else {
          // TODO: backtracking
          throw new Error('no next template, but html has next, so backtracking is needed')
        }
      }
    } else {
      if (nextHTML === null) {
        // TODO: backtracking
        throw new Error('no next html, but template has next, so backtracking is needed')
      } else {
        if (state.loopStates.get(template).isLoopCountIncremented) {
          // TODO: increase loop
          return {
            html: nextHTML,
            template: template
          }
        } else {
          return {
            html: nextHTML,
            template: nextTemplate
          }
        }
      }
    }
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
    while (templateParent instanceof Optional) {
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
      // TODO: implement if template has multiple node
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

export const checkMatch = (htmlRoot, templateRoot) => {
  const nextNodesQueue = [
    new Nodes({ html: htmlRoot, template: templateRoot })
  ]
  while (nextNodesQueue.length > 0) {
    const { template, html } = nextNodesQueue.pop()

    // TODO: template should always be Tag
    if (template instanceof Tag) {
      if (!template.matchWith(html)) {
        continue
      }

      // check if children found in html?
      if (html.children.length > 0) {
        if (template.children.length > 0) {
          // add candidates of html children
          // TODO: add all Tag template candidates
          nextNodesQueue.push(new Nodes({
            html: html.children[0],
            template: template.children[0]
          }))
          continue
        } else {
          // if html has a child and template has no children, this candidate failed
          continue
        }
      } else {
        // check if template has no children
        let templateHasChildren = false
        for (child of template.children) {
          if (!(child instanceof Optional)) {
            templateHasChildren = true
          }
        }
        if (templateHasChildren) {
          // if html has no children and template has a child, this candidate failed
          continue
        }
      }

      // check if next sibling found in html?
      if (html.next !== null) {
        if (template.nextNode() !== null) {
          nextNodesQueue.push(new Nodes({
            html: html.next,
            template: template.nextNode()
          }))
          continue
        } else {
          let parentTemplate = template.parent
          while (parentTemplate instanceof Loop || parentTemplate instanceof Optional) {
            if (parentTemplate instanceof Loop) {
              // if template is children of loop, add loop and it's sibling as candidate
              nextNodesQueue.push(new Nodes({
                html: html.next,
                template: parentTemplate
              }))

              if (parentTemplate.nextNode() !== null) {
                nextNodesQueue.push(new Nodes({
                  html: html.next,
                  template: parentTemplate.nextNode()
                }))
                break
              }
            }
            if (parentTemplate instanceof Optional) {
              // if template is children of optional, add nextNode as candidate
              if (parentTemplate.nextNode() !== null) {
                nextNodesQueue.push(new Nodes({
                  html: html.next,
                  template: parentTemplate.nextNode()
                }))
                break
              }
            }

            parentTemplate = template.parent
          }

          // if html has next and template has no next node, this candidate failed
          continue
        }
      } else {
        // check if template has no next node
        // TODO: check if template has no optional next node
        let templateHasNextNode = template.nextNode() !== null
        let parentTemplate = template.parent
        while (parentTemplate instanceof Optional || parentTemplate instanceof Loop) {
          // TODO: check if parentTemplate has no optional next node
          if (parentTemplate.nextNode() !== null) {
            templateHasNextNode = true
          }
        }
        if (templateHasNextNode) {
          // if template has next node, this candidate failed
          continue
        }
      }

      // if html has no children and no next nodes, check next node of parent
      let parentHTML = html.parent
      let parentTemplate = template.parent
      while (true) {
        while (parentTemplate instanceof Optional || parentTemplate instanceof Loop) {
          if (parentTemplate.nextNode() !== null) {
            // if html has no next node and template has next node, this candidate failed
          }
          parentTemplate = parentTemplate.parent
        }

        if (parentHTML.next === null) {
          break
        }


        parentHTML = html.parent
        parentTemplate = template.parent
      }
      let parentTemplate = template.parent
      while (parentTemplate instanceof Loop || parentTemplate instanceof Optional) {
        if (parentTemplate.nextNode() !== null) {
          break
        }
      }
      nextNodesQueue.push(new Nodes({
        html: html.parent,
        template: template.parent
      }))
    }
  }
  return false
}
