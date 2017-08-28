import fs from 'fs'
import { runRoadrunner } from './runner'
import { roadrunnerFileToTemplate } from './convert'
import { formatHTMLByChrome } from '../html-format'
import { extendTemplateAttributesValue } from '../extend'

export const generateTemplate = async (htmls, preferenceFile) => {
  const formattedHTMLs = await Promise.all(htmls.map(async (html) => {
    return await formatHTMLByChrome(html)
  }))

  const roadrunnerFile = await runRoadrunner(formattedHTMLs, preferenceFile)

  const template = await roadrunnerFileToTemplate(roadrunnerFile)
  return extendTemplateAttributesValue(template, formattedHTMLs)
}
