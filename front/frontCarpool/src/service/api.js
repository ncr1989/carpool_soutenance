import axios from "axios";

const api = axios.create({
  baseURL: "http://127.0.0.1:8000/api",
  
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem("jwt");
  if (token) {
    config.headers["x-auth-token"] = token; 
  }
  return config;
});

export default api;



