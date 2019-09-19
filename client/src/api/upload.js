import http from '@/utils/http'
let path = '/files'

let upload = () => {
  let url = http.baseURL + path + '/index'
  return url
}

export default{
  upload
}
