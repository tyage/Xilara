import { launch } from 'chrome-launcher';
import chrome from 'chrome-remote-interface'

let chromeProcess = null
const launchChrome = async () => {
  if (chromeProcess === null) {
    chromeProcess = await launch({
      chromeFlags: ['--headless']
    })
  }
  return chromeProcess
}

let CDP = null
const launchCDP = async () => {
  const chromeProcess = await launchChrome()
  return new Promise((resolve, reject) => {
    if (CDP === null) {
      chrome({
        port: chromeProcess.port
      }, async (client) => {
        CDP = client
        resolve(CDP)
      })
    } else {
      resolve(CDP)
    }
  })
}

export const exitChrome = () => {
  if (chromeProcess) {
    chromeProcess.kill()
  }
  chromeProcess = null
  CDP = null
}

export const formatHTMLByChrome = async (html) => {
  const { Runtime } = await launchCDP()
  await Runtime.enable()

  const formatProgram = `
(() => {
  const html = atob('${(new Buffer(html)).toString('base64')}')
  const dom = (new DOMParser()).parseFromString(html, 'text/html')
  let result = ''
  for (child of dom.childNodes) {
    if (child && child.outerHTML) {
      result += child.outerHTML
    }
  }
  return result
})()
`
  const { result } = await Runtime.evaluate({expression: formatProgram})

  return result.value
}

process.on('exit', () => {
  exitChrome()
})
