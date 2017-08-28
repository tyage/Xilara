import fs from 'fs'
import assert from 'assert'
import { generateTemplate } from '../../src/roadrunner'
import { isHTMLMatchWithTemplate } from '../../src'

const templateHTMLs = [
  'data/wordpress-count-per-day/datasets/safe-1.html',
  'data/wordpress-count-per-day/datasets/safe-2.html',
  'data/wordpress-count-per-day/datasets/safe-3.html',
  'data/wordpress-count-per-day/datasets/safe-4.html',
]
const roadrunnerPreferenceFile = 'data/wordpress-count-per-day/roadrunner/pref.xml'
const safeHTMLs = [
  'data/wordpress-count-per-day/datasets/safe-1.html',
  'data/wordpress-count-per-day/datasets/safe-2.html',
  'data/wordpress-count-per-day/datasets/safe-3.html',
  'data/wordpress-count-per-day/datasets/safe-4.html',
]
const xssedHTMLs = [
  'data/wordpress-count-per-day/datasets/xssed-1.html'
]

describe('Wordpress Count Per Day', () => {
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
        return isHTMLMatchWithTemplate(html, template).then(({ result }) => {
          assert.equal(result, true)
        })
      })
    })
  })

  xssedHTMLs.map(htmlFile => {
    describe(htmlFile, () => {
      it('should not match with template', () => {
        const html = fs.readFileSync(htmlFile).toString()
        return isHTMLMatchWithTemplate(html, template).then(({ result }) => {
          assert.equal(result, false)
        })
      })
    })
  })
})
