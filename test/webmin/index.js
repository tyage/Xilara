import fs from 'fs'
import assert from 'assert'
import { roadrunnerFileToTemplate } from '../../template/roadrunner-to-template'
import { stringifyTemplate, isHTMLMatchWithTemplate } from '../../template'

const safeHTMLs = [
  'vulnerable-apps/webmin/datasets/safe-1.html',
  'vulnerable-apps/webmin/datasets/safe-2.html',
  'vulnerable-apps/webmin/datasets/safe-3.html',
  'vulnerable-apps/webmin/datasets/validator.html',
]
const xssedHTMLs = [
  'vulnerable-apps/webmin/datasets/xssed-1.html'
]

const roadrunnerXMLFile = 'vulnerable-apps/webmin/roadrunner/webmin00.xml'

describe('Webmin', () => {
  safeHTMLs.map(htmlFile => {
    describe(htmlFile, () => {
      it('should match with template', () => {
        return roadrunnerFileToTemplate(roadrunnerXMLFile).then((template) => {
          const html = fs.readFileSync(htmlFile).toString()
          return isHTMLMatchWithTemplate(html, template)
        }).then((match) => {
          assert.equal(match, true)
        })
      })
    })
  })

  xssedHTMLs.map(htmlFile => {
    describe(htmlFile, () => {
      it('should not match with template', () => {
        return roadrunnerFileToTemplate(roadrunnerXMLFile).then((template) => {
          const html = fs.readFileSync(htmlFile).toString()
          return isHTMLMatchWithTemplate(html, template)
        }).then((match) => {
          assert.equal(match, false)
        })
      })
    })
  })
})
