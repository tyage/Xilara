import fs from 'fs'
import { parseString } from 'xml2js'
import { roadRunnerToTemplate } from './roadrunner-to-template'

const stringifyTemplate = (template, indent = 0) => {
  const elem = '  '.repeat(indent) + '- ' + template + '\n'

  let children = ''
  if (template.children.length > 0) {
    children = template.children.map(e => stringifyTemplate(e, indent + 1)).join('')
  }

  return elem + children
}

const [roadrunnerXMLFile] = process.argv.slice(2)
const buf = fs.readFileSync(roadrunnerXMLFile)
parseString(buf.toString(), {
  explicitChildren: true,
  preserveChildrenOrder: true
}, (err, result) => {
  const template = roadRunnerToTemplate(result.wrapper.expression[0].$$)
  console.log(template.map(t => stringifyTemplate(t)).join(''))
})
