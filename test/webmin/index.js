import { roadrunnerFileToTemplate } from '../../roadrunner-to-template'

const roadrunnerXMLFile = '../../vulnerable-apps/webmin/roadrunner/webmin00.xml'
const template = roadrunnerFileToTemplate(roadrunnerXMLFile)
console.log(template)
