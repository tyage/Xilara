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
        const matches = safeHTMLs.map(htmlFile => {
          const html = fs.readFileSync(htmlFile)
          return isHTMLMatchWithTemplate(html, template)
        })
        return Promise.all(matches)
      }).then((matches) => {
        matches.forEach(match => {
          assert.equal(match, true)
        })
      })
    })
  })

  describe('xssed HTML', () => {
    it('should not match with template', () => {
      return roadrunnerFileToTemplate(roadrunnerXMLFile).then((template) => {
        const matches = xssedHTMLs.map(htmlFile => {
          const html = fs.readFileSync(htmlFile)
          return isHTMLMatchWithTemplate(html, template)
        })
        return Promise.all(matches)
      }).then((matches) => {
        matches.forEach(match => {
          assert.equal(match, false)
        })
      })
    })
  })
})
