const API = "http://localhost:2004/api/v1/"

function get_api_route(route:string) {
  return API + route;
  
}
export {get_api_route}
