// src/api.js
const BASE_URL = 'http://localhost/banque_spa/backend/api';

export const API = {
  auth:         `${BASE_URL}/auth.php`,
  getClients:   `${BASE_URL}/getClients.php`,
  addClient:    `${BASE_URL}/addClient.php`,
  updateClient: `${BASE_URL}/updateClient.php`,
  deleteClient: `${BASE_URL}/deleteClient.php`,
  bilan:        `${BASE_URL}/bilan.php`,
};

export default API;