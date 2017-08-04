import fs from 'fs'
import { exec } from 'child_process'
import { formatHTMLByChrome } from './preprocessing'

const buildDirBase = './build'
export const generateRoadRunnerTemplate = async (htmlFiles, preferenceFile) => {
  const buildDir = `${buildDirBase}/${(new Date()).getTime()}`
  fs.mkdirSync(buildDir)
  const formattedHTMLFiles = await Promise.all(htmlFiles.map(async (htmlFile, i) => {
    const html = fs.readFileSync(htmlFile).toString()
    const formattedHTML = await formatHTMLByChrome(html)
    const formattedHTMLFile = `${buildDir}/${i}.html`
    fs.writeFileSync(formattedHTMLFile, formattedHTML)
    return formattedHTMLFile
  }))
  console.log(formattedHTMLFiles)

  const result = exec(`
cd ../roadrunner &&
gradle run -Pargs='-Nwebmin -O${preferenceFile} ${formattedHTMLFiles.map(f => `../xss-auditor/${f}`).join(' ')}'
`, (err, stdout, stderr) => {
    console.log('stdout: ' + stdout);
    console.log('stderr: ' + stderr);
    if (err !== null) {
      console.log('exec error: ' + err);
    }
  })
}

/*
generateRoadRunnerTemplate(
  [
    './vulnerable-apps/webmin/datasets/safe-1.html',
    './vulnerable-apps/webmin/datasets/safe-2.html',
    './vulnerable-apps/webmin/datasets/safe-3.html',
  ],
  './examples/webmin/webmin.xml'
)
*/
