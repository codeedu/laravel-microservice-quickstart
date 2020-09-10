import HttpResource from "./http-resource";
import {httpVideo} from "./index";

const categoryHttp = new HttpResource(httpVideo, "categories");

export default categoryHttp;
