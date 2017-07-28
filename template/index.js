import { DomHandler, Parser } from 'htmlparser2'
import { checkMatch } from './matching'

export const stringifyTemplate = (template, indent = 0) => {
  const elem = ' '.repeat(indent) + '- ' + template + '\n'

  let children = ''
  if (template.children.length > 0) {
    children = template.children.map(e => stringifyTemplate(e, indent + 1)).join('')
  }

  return elem + children
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
      // TODO: consider about there being multiple top level node
      let firstHTML = null
      for (let d of dom) {
        if (d.type !== 'directive') {
          firstHTML = d
          break
        }
      }
      resolve(checkMatch(firstHTML, template))
    })
  })
}
