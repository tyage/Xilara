import { parseString } from 'xml2js'

export const stringifyTemplate = (template, indent = 0) => {
  const elem = ' '.repeat(indent) + '- ' + template + '\n'

  let children = ''
  if (template.children.length > 0) {
    children = template.children.map(e => stringifyTemplate(e, indent + 1)).join('')
  }

  return elem + children
}

export const isHTMLMatchWithTemplate = (html, template) => {
  const checkMatch = (htmlRoot, templateRoot) => {
    const checkHTMLStack = [htmlRoot]
    const checkTemplateStack = [templateRoot]

    while (checkHTMLStack.length !== 0 && checkTemplateStack.length !== 0) {
      const html = checkHTMLStack.pop()
      const template = checkTemplateStack.pop()
      console.log(checkTemplateStack.join(' '))
      console.log(`check ${html['#name']} and ${template}`)

      if (!template.matchWith(html)) {
        return false
      }

      if (html.$$) {
        checkHTMLStack.push(...Array.from(html.$$).reverse())
      }
      checkTemplateStack.push(...Array.from(template.children).reverse())
    }

    return checkHTMLStack.length === 0 && checkTemplateStack.length === 0
  }

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
