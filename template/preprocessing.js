import { launch } from 'chrome-launcher';
import chrome from 'chrome-remote-interface'

const launchCDP = (chromeProcess) => {
  return new Promise((resolve, reject) => {
    chrome({
      port: chromeProcess.port
    }, async (client) => {
        resolve(client)
    })
  })
}

export const formatHTMLByChrome = async (html) => {
  const chromeProcess = await launch({
    chromeFlags: ['--headless']
  })
  const { Runtime } = await launchCDP(chromeProcess)
  await Runtime.enable()

  const formatProgram = `
const html = ${JSON.stringify(html)}
const dom = (new DOMParser()).parseFromString(html, 'text/html')
let result = ''
for (child of dom.childNodes) {
  if (child && child.outerHTML) {
    result += child.outerHTML
  }
}
result
`
  const { result } = await Runtime.evaluate({expression: formatProgram})

  chromeProcess.kill()

  return result.value
}
