import fs from 'fs'
import assert from 'assert'
import { roadrunnerFileToTemplate } from '../../src/roadrunner-to-template'
import { stringifyTemplate, isHTMLMatchWithTemplate } from '../../src'
import { generateRoadRunnerTemplate } from '../../src/generate-roadrunner-template'

const templateHTMLs = [
  'data/webmin/datasets/safe-1.html',
  'data/webmin/datasets/safe-2.html',
  'data/webmin/datasets/safe-3.html',
]
const roadrunnerPreferenceFile = 'data/webmin/roadrunner/webmin.xml'
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
  let roadrunnerFile = null
  before(function(done) {
    this.timeout(10000)
    generateRoadRunnerTemplate(templateHTMLs, roadrunnerPreferenceFile).then((file) => {
      roadrunnerFile = file
      done()
    })
  })

  describe('roadrunner template file', () => {
    it('should be created', () => {
      assert(roadrunnerFile !== null)
    })
  })

  safeHTMLs.map(htmlFile => {
    describe(htmlFile, () => {
      it('should match with template', () => {
        return roadrunnerFileToTemplate(roadrunnerFile).then((template) => {
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
        return roadrunnerFileToTemplate(roadrunnerFile).then((template) => {
          const html = fs.readFileSync(htmlFile).toString()
          return isHTMLMatchWithTemplate(html, template)
        }).then((match) => {
          assert.equal(match, false)
        })
      })
    })
  })
})
