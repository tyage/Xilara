import fs from 'fs'
import { spawnSync } from 'child_process'

const buildDirBase = './build'
const roadrunnerDir = './roadrunner'
export const runRoadrunner = async (htmlFiles, preferenceFile) => {
  const buildDir = `${buildDirBase}/${(new Date()).getTime()}`
  fs.mkdirSync(buildDir)

  // execute roadrunner
  const result = spawnSync('./gradlew', [
    `-Pargs=-Ntemplate -O../${preferenceFile} ${htmlFiles.map(f => `../${f}`).join(' ')}`,
    'run'
  ], {
    cwd: roadrunnerDir
  })

  // move result to build dir
  fs.renameSync(`${roadrunnerDir}/output/template`, `${buildDir}/roadrunner`)

  return `${buildDir}/roadrunner/template00.xml`
}
