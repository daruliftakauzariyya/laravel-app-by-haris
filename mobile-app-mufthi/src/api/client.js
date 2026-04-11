import axios from 'axios';

const client = axios.create({
  baseURL: 'https://new.daruliftakauzariyya.com/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Interceptor for Auth Token
client.interceptors.request.use((config) => {
  const token = localStorage.getItem('mufti_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default client;
