import { isHTMLMatchWithTemplate } from './'

export const extendTemplateAttributesValue = (template, htmls) => {
  htmls.map(async (html) => {
    return await isHTMLMatchWithTemplate(html, template)
  }).map(({ result, matchMap }) => {
    if (!result) {
      console.warn('html not matched with template in extendTemplateAttributesValue')
      return
    }

    for (let [ h, t ] of matchMap.entries()) {
      Object.keys(h.attribs).forEach(k => {
        if (!t.attrs.has(k) || !(t.attrs.get(k) instanceof Array)) {
          t.attrs.set(k, [])
        }
        const list = t.attrs.get(k)
        list.push(h.attribs[k])
      })
    }
  })

  return template
}
