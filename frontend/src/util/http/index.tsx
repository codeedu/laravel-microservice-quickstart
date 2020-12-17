import axios from 'axios'

const httpVideo = axios.create({
    baseURL: process.env.REACT_APP_MICRO_VIDEO_API_URL
})


export default httpVideo;