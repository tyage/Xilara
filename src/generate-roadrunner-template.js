import fs from 'fs'
import { spawnSync } from 'child_process'
import { formatHTMLByChrome } from './preprocessing'

const buildDirBase = './build'
const roadrunnerDir = './roadrunner'
export const generateRoadRunnerTemplate = async (htmlFiles, preferenceFile) => {
  const buildDir = `${buildDirBase}/${(new Date()).getTime()}`
  fs.mkdirSync(buildDir)

  // format html with chrome
  const formattedHTMLFiles = await Promise.all(htmlFiles.map(async (htmlFile, i) => {
    const html = fs.readFileSync(htmlFile).toString()
    const formattedHTML = await formatHTMLByChrome(html)
    const formattedHTMLFile = `${buildDir}/${i}.html`
    fs.writeFileSync(formattedHTMLFile, formattedHTML)
    return formattedHTMLFile
  }))

  // execute roadrunner
  const result = spawnSync('./gradlew', [
    `-Pargs=-Ntemplate -O../${preferenceFile} ${formattedHTMLFiles.map(f => `../${f}`).join(' ')}`,
    'run'
  ], {
    cwd: roadrunnerDir
  })

  // move result to build dir
  fs.renameSync(`${roadrunnerDir}/output/template`, `${buildDir}/roadrunner`)
  return `${buildDir}/roadrunner/template00.xml`
}
