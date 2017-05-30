import { roadrunnerFileToTemplate } from '../'

const roadrunnerXMLFile = '../../vulnerable-apps/webmin/roadrunner/webmin00.xml'
const template = roadrunnerFileToTemplate(roadrunnerXMLFile)
