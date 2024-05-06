const DEBUG = false;

let API = "";
if (DEBUG) {
  API = "http://localhost:2003/api/v1/index.php";
} else {
  API = "https://www.spcjaffna-beta.org/api/v1/index.php";
}

function get_api_route(route:string) {
  return API + "?p=" + route;
  
}
export {get_api_route}
