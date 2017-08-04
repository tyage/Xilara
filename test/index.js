import fs from 'fs'
import assert from 'assert'
import { isHTMLMatchWithTemplate } from '../template'
import { Node, Tag, Optional, Loop, Ignore } from '../template/nodes'

const createTag = (name, attrs, children) => {
  const tag = new Tag(name, attrs || new Map())
  if (children) {
    tag.addChildren(children)
  }
  return tag
}

// div[id=main]
//  |- ul[class=list,id=list]
//     |- li
//  |- p
describe('HTML Tree', () => {
  const template = createTag('html', null, [
    createTag('head', null, []),
    createTag('body', null, [
      createTag('div', new Map([ ['id', 'main'] ]), [
        createTag('ul', new Map([ ['id', 'list'], ['class', 'list'] ]), [
          createTag('li')
        ]),
        createTag('p')
      ])
    ])
  ])
  template.parent = new Node()

  const matchedHTMLs = [
    '<div id="main"><ul id="list" class="list"><li>aaa</li></ul><p></p></div>',
    '<div id="main"><ul id="list" class="list"><li>bbb</li></ul><p></p></div>'
  ]
  const notMatchedHTMLs = [
    '',
    '<div id="main"></div>',
    '<div><ul id="list" class="list"><li>aaa</li></ul><p></p></div>',
    '<div><ul id="nonlist" class="nonlist"><li>aaa</li></ul><p></p></div>',
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
