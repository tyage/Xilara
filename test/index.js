import fs from 'fs'
import assert from 'assert'
import { isHTMLMatchWithTemplate } from '../template'
import { Node, Tag, Optional, Loop, Ignore } from '../template/nodes'

// div[id=main]
//  |- ul[class=list,id=list]
//     |- li
//  |- p
describe('HTML Tree', () => {
  const ul = new Tag('ul', new Map([ ['id', 'list'], ['class', 'list'] ]))
  ul.addChildren([ new Tag('li') ])
  const div = new Tag('div', new Map([ ['id', 'main'] ]))
  div.addChildren([
    ul,
    new Tag('p')
  ])
  div.parent = new Node()
  const template = div

  const matchedHTMLs = [
    '<div id="main"><ul id="list" class="list"><li>aaa</li></ul><p></p></div>',
    '<div id="main"><ul id="list" class="list"><li>bbb</li></ul><p></p></div>'
  ]
  const notMatchedHTMLs = [
    '',
    '<div id="main"></div>',
    // changing or removal of attr is ignored
    //'<div><ul id="list" class="list"><li>aaa</li></ul><p></p></div>',
    //'<div><ul id="nonlist" class="nonlist"><li>aaa</li></ul><p></p></div>',
    '<div id="main"><ul class="list" id="list"><li>aaa</li><p></p></ul></div>',
    '<div id="main"><ul class="list" id="list"><div>aaa</div></ul><p></p></div>'
  ]

  matchedHTMLs.forEach(html => {
    describe(`matched HTML ${html}`, () => {
      it ('should match with template', () => {
        return isHTMLMatchWithTemplate(html, template).then(match => assert.equal(match, true))
      })
    })
  })
  notMatchedHTMLs.forEach(html => {
    describe(`not matched HTML ${html}`, () => {
      it ('should not match with template', () => {
        return isHTMLMatchWithTemplate(html, template).then(match => assert.equal(match, false))
      })
    })
  })
})
