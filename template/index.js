export const stringifyTemplate = (template, indent = 0) => {
  const elem = '  '.repeat(indent) + '- ' + template + '\n'

  let children = ''
  if (template.children.length > 0) {
    children = template.children.map(e => stringifyTemplate(e, indent + 1)).join('')
  }

  return elem + children
}

export const isHTMLMatchWithTemplate = (html, template) => {
  return true
}
