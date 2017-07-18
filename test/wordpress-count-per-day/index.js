import fs from 'fs'
import assert from 'assert'
import { roadrunnerFileToTemplate } from '../../template/roadrunner-to-template'
import { stringifyTemplate, isHTMLMatchWithTemplate } from '../../template'

const safeHTMLs = [
  'vulnerable-apps/wordpress-count-per-day/datasets/safe-1.html',
  'vulnerable-apps/wordpress-count-per-day/datasets/safe-2.html',
  'vulnerable-apps/wordpress-count-per-day/datasets/safe-3.html',
  'vulnerable-apps/wordpress-count-per-day/datasets/safe-4.html',
]
const xssedHTMLs = [
  'vulnerable-apps/wordpress-count-per-day/datasets/xssed-1.html'
]

const roadrunnerXMLFile = 'vulnerable-apps/wordpress-count-per-day/roadrunner/wordpress-count-per-day00.xml'

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
