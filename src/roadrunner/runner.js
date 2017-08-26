import fs from 'fs'
import tmp from 'tmp-promise'
import { spawnSync } from 'child_process'

const roadrunnerDir = './roadrunner'
export const runRoadrunner = async (htmls, preferenceFile) => {
  const { path: buildDir } = await tmp.dir({ prefix: `roadrunner-${(new Date()).getTime()}-` })

  const htmlFiles = await Promise.all(htmls.map(async (html, i) => {
    const formattedHTMLFile = `${buildDir}/${i}.html`
    fs.writeFileSync(formattedHTMLFile, html)
    return formattedHTMLFile
  }))

  // execute roadrunner
  const result = spawnSync('./gradlew', [
    `-Pargs=-Ntemplate -O../${preferenceFile} ${htmlFiles.join(' ')}`,
    'run'
  ], {
    cwd: roadrunnerDir
  })

  // move result to build dir
  fs.renameSync(`${roadrunnerDir}/output/template`, `${buildDir}/roadrunner`)

  return `${buildDir}/roadrunner/template00.xml`
}
