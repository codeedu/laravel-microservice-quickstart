import HttpResource from "./http-resource";
import {httpVideo} from "./index";

const videoHttp = new HttpResource(httpVideo, "videos");

export default videoHttp;
