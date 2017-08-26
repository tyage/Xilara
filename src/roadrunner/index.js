import fs from 'fs'
import { runRoadrunner } from './runner'
import { roadrunnerFileToTemplate } from './convert'
import { formatHTMLByChrome } from '../preprocessing'

const buildDirBase = './build'
export const generateTemplate = async (htmlFiles, preferenceFile) => {
  const buildDir = `${buildDirBase}/${(new Date()).getTime()}`
  fs.mkdirSync(buildDir)

  const formattedHTMLFiles = await Promise.all(htmlFiles.map(async (htmlFile, i) => {
    const html = fs.readFileSync(htmlFile).toString()
    const formattedHTML = await formatHTMLByChrome(html)
    const formattedHTMLFile = `${buildDir}/${i}.html`
    fs.writeFileSync(formattedHTMLFile, formattedHTML)
    return formattedHTMLFile
  }))

  const roadrunnerFile = await runRoadrunner(formattedHTMLFiles, preferenceFile)

  return await roadrunnerFileToTemplate(roadrunnerFile)
}
