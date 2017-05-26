import fs from 'fs'

const [roadrunnerXMLFile] = process.argv.slice(2)
const buf = fs.readFileSync(roadrunnerXMLFile)
roadrunnerXML = buf.toString()
