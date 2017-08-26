import fs from 'fs'
import assert from 'assert'
import { generateTemplate } from '../../src/roadrunner'
import { isHTMLMatchWithTemplate } from '../../src'

const templateHTMLs = [
  'data/webmin/datasets/safe-1.html',
  'data/webmin/datasets/safe-2.html',
  'data/webmin/datasets/safe-3.html',
]
const roadrunnerPreferenceFile = 'data/webmin/roadrunner/pref.xml'
const safeHTMLs = [
  'data/webmin/datasets/safe-1.html',
  'data/webmin/datasets/safe-2.html',
  'data/webmin/datasets/safe-3.html',
  'data/webmin/datasets/validator.html',
]
const xssedHTMLs = [
  'data/webmin/datasets/xssed-1.html'
]

describe('Webmin', () => {
  let template = null
  before(function(done) {
    this.timeout(10000)
    const htmls = templateHTMLs.map((htmlFile) => fs.readFileSync(htmlFile).toString())
    generateTemplate(htmls, roadrunnerPreferenceFile).then((t) => {
      template = t
      done()
    })
  })

  describe('template', () => {
    it('should be created', () => {
      assert(template !== null)
    })
  })

  safeHTMLs.map(htmlFile => {
    describe(htmlFile, () => {
      it('should match with template', () => {
        const html = fs.readFileSync(htmlFile).toString()
        return isHTMLMatchWithTemplate(html, template).then((match) => {
          assert.equal(match, true)
        })
      })
    })
  })

  xssedHTMLs.map(htmlFile => {
    describe(htmlFile, () => {
      it('should not match with template', () => {
        const html = fs.readFileSync(htmlFile).toString()
        return isHTMLMatchWithTemplate(html, template).then((match) => {
          assert.equal(match, false)
        })
      })
    })
  })
})
