import { Node, Tag, Optional, Loop, Ignore } from './nodes'

class Nodes {
  constructor({ html, template }) {
    this.html = html
    this.template = template
  }
}

// return nodes which are candidates of first Tag element
// with optional, nonOptional tag
const getFirstChildTemplateTags = (template) => {
  const optionalCandidates = []
  const nonOptionalCandidates = []

  for (let childNode of template.children) {
    if (childNode instanceof Tag) {
      nonOptionalCandidates.push(childNode)
    } else if (childNode instanceof Optional) {
      const { all } = getFirstChildTemplateTags(childNode)
      optionalCandidates.push(...all)
    } else if (childNode instanceof Loop) {
      const { optional, nonOptional } = getFirstChildTemplateTags(childNode)
      optionalCandidates.push(...optional)
      nonOptionalCandidates.push(...nonOptional)
    } else {
      throw new Error('no such element is available')
    }

    // if non optional node exists, it is the last candidate
    if (nonOptionalCandidates.length > 0) {
      break
    }
  }

  return {
    optional: optionalCandidates,
    nonOptional: nonOptionalCandidates,
    all: [ ...optionalCandidates, ...nonOptionalCandidates ]
  }
}

// return next template node candidates
const getNextTemplateTags = (template) => {
  const optionalCandidates = []
  const nonOptionalCandidates = []

  // search candidates in next node
  let nextNode = template.nextNode()
  while (nextNode !== null) {
    if (nextNode instanceof Tag) {
      nonOptionalCandidates.push(nextNode)
    } else if (nextNode instanceof Optional) {
      const { all } = getFirstChildTemplateTags(nextNode)
      optionalCandidates.push(...all)
    } else if (nextNode instanceof Loop) {
      const { optional, nonOptional } = getFirstChildTemplateTags(nextNode)
      optionalCandidates.push(...optional)
      nonOptionalCandidates.push(...nonOptional)

    } else {
      throw new Error('no such element is available')
    }

    // if non optional node exists, it is the last candidate
    if (nonOptionalCandidates.length > 0) {
      break
    }

    nextNode = nextNode.nextNode()
  }

  // if only optional candidates found in next nodes, search next node in parent
  if (nonOptionalCandidates.length === 0) {
    // add first node of parent loop element as optional candidates
    if (template.parent instanceof Loop) {
      const { all } = getFirstChildTemplateTags(template.parent)
      optionalCandidates.push(...all)
    }

    // if template.parent is Loop or Optional, it may have next node
    if (template.parent instanceof Loop || template.parent instanceof Optional) {
      const { optional, nonOptional } = getNextTemplateTags(template.parent)
      optionalCandidates.push(...optional)
      nonOptionalCandidates.push(...nonOptional)
    }
  }

  return {
    optional: optionalCandidates,
    nonOptional: nonOptionalCandidates,
    all: [ ...optionalCandidates, ...nonOptionalCandidates ]
  }
}

// return parent template tag node
const getParentTemplateTag = (template) => {
  let templateParent = template.parent
  while (!(templateParent instanceof Tag)) {
    // XXX: it may throw error
    templateParent = templateParent.parent
  }
  return templateParent
}

export const checkMatch = (htmlRoot, templateRoot) => {
  const nextNodesQueue = [
    new Nodes({ html: htmlRoot, template: templateRoot })
  ]
  while (nextNodesQueue.length > 0) {
    const { template, html } = nextNodesQueue.pop()

    // logging
    const htmlStr = html ? `<${html.name} ${Object.keys(html.attribs).join(' ')}>` : 'null'
    console.log(`html: ${htmlStr}, template: ${template}`)

    // if template not matched with html, this candidate failed
    if (!template.matchWith(html)) {
      continue
    }

    // if template has Ignore children, dont consider children of html
    // TODO: consider if children of template contains not only Ignore
    const ignoreChildrenCheck = template.children.length > 0 && template.children[0] instanceof Ignore

    if (!ignoreChildrenCheck) {
      // check if children found in html?
      if (html.children.length > 0) {
        // add candidates of html children
        const { all } = getFirstChildTemplateTags(template)
        for (let templateCandidate of all) {
          nextNodesQueue.push(new Nodes({
            html: html.children[0],
            template: templateCandidate
          }))
        }
        continue
      }
      // check if template has no non-optional children
      const { nonOptional: nonOptionalTemplateChild } = getFirstChildTemplateTags(template)
      if (nonOptionalTemplateChild.length > 0) {
        // if html has no children and template has a child, this candidate failed
        continue
      }
    }

    // check if next sibling found in html?
    if (html.next !== null) {
      const { all } = getNextTemplateTags(template)
      for (let templateCandidate of all) {
        nextNodesQueue.push(new Nodes({
          html: html.next,
          template: templateCandidate
        }))
      }
      continue
    }
    // check if template has no non-optional next node
    const { nonOptional: nonOptionalTemplateNext } = getNextTemplateTags(template)
    if (nonOptionalTemplateNext.length > 0) {
      // if template has next node, this candidate failed
      continue
    }

    // if html has no children and no next nodes, check next node of parent
    let htmlParent = html.parent
    let templateParent = getParentTemplateTag(template)
    let searchParentFailed = false
    while (true) {
      if (htmlParent.next !== null) {
        break
      } else {
        // check if template has no non-optional next node
        const { nonOptional: nonOptionalTemplateParentNext } = getNextTemplateTags(templateParent)
        if (nonOptionalTemplateParentNext.length > 0) {
          searchParentFailed = true
          break
        }
      }

      // if reached to root html, check if template reached to root template
      if (htmlParent === htmlRoot) {
        if (templateParent === templateParent) {
          // if reached to root element, matching completed
          return true
        } else {
          searchParentFailed = true
          break
        }
      }

      htmlParent = htmlParent.parent
      templateParent = getParentTemplateTag(templateParent)
    }
    if (searchParentFailed) {
      continue
    }

    // after searching parent which has next node, add candidates
    const { all } = getNextTemplateTags(templateParent)
    for (let templateCandidate of all) {
      nextNodesQueue.push(new Nodes({
        html: htmlParent.next,
        template: templateCandidate
      }))
    }
  }

  // if all candidates failed, return false
  return false
}
