import fs from 'fs'
import assert from 'assert'
import { roadrunnerFileToTemplate } from '../../template/roadrunner-to-template'
import { stringifyTemplate, isHTMLMatchWithTemplate } from '../../template'

const safeHTMLs = [
  'vulnerable-apps/webmin/datasets/safe-1.html',
  'vulnerable-apps/webmin/datasets/safe-2.html',
  'vulnerable-apps/webmin/datasets/safe-3.html'
]
const xssedHTMLs = [
  'vulnerable-apps/webmin/datasets/xssed-1.html'
]

const roadrunnerXMLFile = 'vulnerable-apps/webmin/roadrunner/webmin00.xml'

describe('Webmin', () => {
  describe('safe HTML', () => {
    it('should match with template', () => {
      return roadrunnerFileToTemplate(roadrunnerXMLFile).then((template) => {
        safeHTMLs.forEach(htmlFile => {
          const html = fs.readFileSync(htmlFile)
          assert.equal(isHTMLMatchWithTemplate(html, template), true)
        })
      })
    })
  })

  describe('xssed HTML', () => {
    it('should not match with template', () => {
      return roadrunnerFileToTemplate(roadrunnerXMLFile).then((template) => {
        xssedHTMLs.forEach(htmlFile => {
          const html = fs.readFileSync(htmlFile)
          assert.equal(isHTMLMatchWithTemplate(html, template), false)
        })
      })
    })
  })
})
