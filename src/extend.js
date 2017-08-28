import { isHTMLMatchWithTemplate } from './'

export const extendTemplateWithAttributesValue = (template, htmls) => {
  htmls.forEach(async (html) => {
    const { result, matchMap } = await isHTMLMatchWithTemplate(html, template)
    for (let [ h, t ] of matchMap.entries()) {
      Object.keys(h.attribs).forEach(k => {
        if (t.attrs.has(k)) {
          t.attrs.set(k, [])
        }
        const list = t.attrs.get(k)
        list.push(h.attribs[k])
      })
    }
  })

  return template
}
