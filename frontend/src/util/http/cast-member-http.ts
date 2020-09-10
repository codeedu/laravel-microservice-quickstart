import HttpResource from "./http-resource";
import {httpVideo} from "./index";

const castMemberHttp = new HttpResource(httpVideo, "cast_members");

export default castMemberHttp;
