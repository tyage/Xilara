import { isHTMLMatchWithTemplate } from './'

export const extendTemplateAttributesValue = async (template, htmls) => {
  const results = await Promise.all(htmls.map(async (html) => {
    return await isHTMLMatchWithTemplate(html, template)
  }))
  results.map(({ result, matchMap }) => {
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
