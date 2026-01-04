# Frontend Integráció Útmutató

Ez az útmutató bemutatja, hogyan integrálhatod a MAAF backend-et különböző frontend frameworkökkel.

## Tartalomjegyzék

1. [React Integráció](#react-integráció)
2. [Vue.js Integráció](#vuejs-integráció)
3. [Vanilla JavaScript](#vanilla-javascript)
4. [CORS Beállítás](#cors-beállítás)
5. [Autentikáció](#autentikáció)
6. [API Hívások](#api-hívások)

---

## React Integráció

### 1. Projekt Létrehozása

```bash
# React projekt létrehozása
npx create-react-app my-frontend
cd my-frontend

# Vagy Vite használata (gyorsabb)
npm create vite@latest my-frontend -- --template react
cd my-frontend
npm install
```

### 2. API Service Létrehozása

Hozz létre egy `src/services/api.js` fájlt:

```javascript
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000';

class ApiService {
  constructor() {
    this.baseURL = API_BASE_URL;
    this.token = localStorage.getItem('token');
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const config = {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        ...(this.token && { Authorization: `Bearer ${this.token}` }),
        ...options.headers,
      },
    };

    const response = await fetch(url, config);
    
    if (!response.ok) {
      throw new Error(`API Error: ${response.statusText}`);
    }

    return response.json();
  }

  get(endpoint) {
    return this.request(endpoint, { method: 'GET' });
  }

  post(endpoint, data) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  put(endpoint, data) {
    return this.request(endpoint, {
      method: 'PUT',
      body: JSON.stringify(data),
    });
  }

  delete(endpoint) {
    return this.request(endpoint, { method: 'DELETE' });
  }

  setToken(token) {
    this.token = token;
    localStorage.setItem('token', token);
  }

  clearToken() {
    this.token = null;
    localStorage.removeItem('token');
  }
}

export default new ApiService();
```

### 3. Auth Context Létrehozása

```javascript
// src/contexts/AuthContext.jsx
import React, { createContext, useContext, useState, useEffect } from 'react';
import api from '../services/api';

const AuthContext = createContext();

export function useAuth() {
  return useContext(AuthContext);
}

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    checkAuth();
  }, []);

  async function checkAuth() {
    try {
      const token = localStorage.getItem('token');
      if (token) {
        api.setToken(token);
        const userData = await api.get('/auth/me');
        setUser(userData);
      }
    } catch (error) {
      api.clearToken();
    } finally {
      setLoading(false);
    }
  }

  async function login(email, password) {
    const response = await api.post('/auth/login', { email, password });
    api.setToken(response.token);
    setUser(response.user);
    return response;
  }

  function logout() {
    api.clearToken();
    setUser(null);
  }

  return (
    <AuthContext.Provider value={{ user, login, logout, loading }}>
      {children}
    </AuthContext.Provider>
  );
}
```

### 4. Komponens Használat

```javascript
// src/components/Login.jsx
import { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';

export default function Login() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const { login } = useAuth();

  async function handleSubmit(e) {
    e.preventDefault();
    try {
      await login(email, password);
      // Redirect to dashboard
    } catch (error) {
      alert('Login failed');
    }
  }

  return (
    <form onSubmit={handleSubmit}>
      <input
        type="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        placeholder="Email"
      />
      <input
        type="password"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        placeholder="Password"
      />
      <button type="submit">Login</button>
    </form>
  );
}
```

### 5. Environment Változók

Hozz létre egy `.env` fájlt:

```env
REACT_APP_API_URL=http://localhost:8000
```

---

## Vue.js Integráció

### 1. Projekt Létrehozása

```bash
npm create vue@latest my-frontend
cd my-frontend
npm install
```

### 2. API Service Létrehozása

Hozz létre egy `src/services/api.js` fájlt:

```javascript
const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

class ApiService {
  constructor() {
    this.baseURL = API_BASE_URL;
    this.token = localStorage.getItem('token');
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const config = {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        ...(this.token && { Authorization: `Bearer ${this.token}` }),
        ...options.headers,
      },
    };

    const response = await fetch(url, config);
    
    if (!response.ok) {
      throw new Error(`API Error: ${response.statusText}`);
    }

    return response.json();
  }

  get(endpoint) {
    return this.request(endpoint, { method: 'GET' });
  }

  post(endpoint, data) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }

  setToken(token) {
    this.token = token;
    localStorage.setItem('token', token);
  }

  clearToken() {
    this.token = null;
    localStorage.removeItem('token');
  }
}

export default new ApiService();
```

### 3. Composable Használat

```javascript
// src/composables/useAuth.js
import { ref } from 'vue';
import api from '../services/api';

export function useAuth() {
  const user = ref(null);
  const loading = ref(true);

  async function login(email, password) {
    const response = await api.post('/auth/login', { email, password });
    api.setToken(response.token);
    user.value = response.user;
    return response;
  }

  function logout() {
    api.clearToken();
    user.value = null;
  }

  return {
    user,
    loading,
    login,
    logout,
  };
}
```

---

## Vanilla JavaScript

### API Service

```javascript
// api.js
const API_BASE_URL = 'http://localhost:8000';

class ApiService {
  constructor() {
    this.baseURL = API_BASE_URL;
    this.token = localStorage.getItem('token');
  }

  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const config = {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        ...(this.token && { Authorization: `Bearer ${this.token}` }),
        ...options.headers,
      },
    };

    const response = await fetch(url, config);
    return response.json();
  }

  get(endpoint) {
    return this.request(endpoint, { method: 'GET' });
  }

  post(endpoint, data) {
    return this.request(endpoint, {
      method: 'POST',
      body: JSON.stringify(data),
    });
  }
}

export default new ApiService();
```

---

## CORS Beállítás

A MAAF backend automatikusan támogatja a CORS-t. A beállítások a `config/cors.php` fájlban találhatók:

```php
return [
    'enabled' => true,
    'allowed_origins' => [
        'http://localhost:5173',  // Vite default
        'http://localhost:3000',  // React default
        'http://localhost:8080',  // Vue default
    ],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
];
```

---

## Autentikáció

### JWT Token Kezelés

A backend JWT tokeneket használ autentikációhoz. A token a `Authorization` header-ben kell legyen:

```javascript
headers: {
  'Authorization': `Bearer ${token}`
}
```

### Login Példa

```javascript
async function login(email, password) {
  const response = await fetch('http://localhost:8000/auth/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ email, password }),
  });

  const data = await response.json();
  localStorage.setItem('token', data.token);
  return data;
}
```

---

## API Hívások

### Példa: Adatok Lekérése

```javascript
// React példa
import { useEffect, useState } from 'react';
import api from './services/api';

function UserList() {
  const [users, setUsers] = useState([]);

  useEffect(() => {
    async function fetchUsers() {
      try {
        const data = await api.get('/users');
        setUsers(data);
      } catch (error) {
        console.error('Failed to fetch users', error);
      }
    }
    fetchUsers();
  }, []);

  return (
    <ul>
      {users.map(user => (
        <li key={user.id}>{user.name}</li>
      ))}
    </ul>
  );
}
```

### Példa: Adatok Létrehozása

```javascript
async function createUser(userData) {
  try {
    const response = await api.post('/users', userData);
    return response;
  } catch (error) {
    console.error('Failed to create user', error);
    throw error;
  }
}
```

---

## További Források

- [MAAF Core Dokumentáció](https://github.com/mimimami/maaf-core)
- [React Dokumentáció](https://react.dev)
- [Vue.js Dokumentáció](https://vuejs.org)
- [Fetch API Dokumentáció](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)

