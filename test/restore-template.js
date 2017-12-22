import fs from 'fs'
import assert from 'assert'
import { generateTemplate } from '../src/roadrunner'
import { isHTMLMatchWithTemplate } from '../src'

const roadrunnerPreferenceFile = 'data/webmin/roadrunner/pref.xml'

// div[id=main]
//  |- ul[class=list,id=list]
//     |- li
//  |- optional
//     |- p
describe('Restore Right OR Left Template', () => {
  const optionalTemplate = (isRight) => `
<div class="tablenav bottom">
  <p></p>
  ${isRight ? '<div class="a"></div>' : '<ul class="b"></ul>'}
  <br />
</div>
`;
  const htmls = [ optionalTemplate(true), optionalTemplate(false) ];

  let template = null
  before(function(done) {
    this.timeout(10000)
    generateTemplate(htmls, roadrunnerPreferenceFile).then((t) => {
      template = t
      done()
    }).catch(e => console.log(e))
  })

  describe(`matched HTML`, () => {
    it ('should match with template', () => {
      console.log(template)
    })
  })
})
