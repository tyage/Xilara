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

describe('Wordpress Count Per Day', () => {
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
