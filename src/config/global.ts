const API = "http://localhost:2004/api/v1/index.php"

function get_api_route(route:string) {
  return API + "?p=" + route;
  
}
export {get_api_route}
