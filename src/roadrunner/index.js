import fs from 'fs'
import { runRoadrunner } from './runner'
import { roadrunnerFileToTemplate } from './convert'
import { formatHTMLByChrome } from '../html-format'
import { extendTemplateAttributesValue } from '../extend'

export const generateTemplateFromRoadrunnerFile = async (roadrunnerFile, formattedHTMLs) => {
  const template = await roadrunnerFileToTemplate(roadrunnerFile)
  return await extendTemplateAttributesValue(template, formattedHTMLs)
}
export const generateTemplate = async (htmls, preferenceFile) => {
  const formattedHTMLs = await Promise.all(htmls.map(async (html) => {
    return await formatHTMLByChrome(html)
  }))

  const roadrunnerFile = await runRoadrunner(formattedHTMLs, preferenceFile)
  console.info(`roadrunner file was created at: ${roadrunnerFile}`)

  return await generateTemplateFromRoadrunnerFile(roadrunnerFile, formattedHTMLs)
}
