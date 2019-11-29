import HttpResource from "./http-resource";
import {httpVideo} from "./index";

const genreHttp = new HttpResource(httpVideo, "genres");

export default genreHttp;
