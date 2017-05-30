import fs from 'fs'
import { should } from 'chai'
import { roadrunnerFileToTemplate } from '../../template/roadrunner-to-template'
import { stringifyTemplate, isHTMLMatchWithTemplate } from '../../template'

should()

const safeHTMLs = [
  'vulnerable-apps/webmin/datasets/safe-1.html',
  'vulnerable-apps/webmin/datasets/safe-2.html',
  'vulnerable-apps/webmin/datasets/safe-3.html'
]
const xssedHTMLs = [
  'vulnerable-apps/webmin/datasets/xssed-1.html'
]

const roadrunnerXMLFile = 'vulnerable-apps/webmin/roadrunner/webmin00.xml'
roadrunnerFileToTemplate(roadrunnerXMLFile).then((template) => {
  safeHTMLs.forEach(htmlFile => {
    const html = fs.readFileSync(htmlFile)
    isHTMLMatchWithTemplate(html, template).should.equal(true)
  })
  xssedHTMLs.forEach(htmlFile => {
    const html = fs.readFileSync(htmlFile)
    isHTMLMatchWithTemplate(html, template).should.equal(false)
  })
}).catch((err) => {
  console.log(err)
})
