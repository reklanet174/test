const BASE = '/api/notes';

async function request(url, options = {}) {
  const res = await fetch(url, {
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      ...(options.headers || {}),
    },
    ...options,
  });

  let payload = null;
  try {
    payload = await res.json();
  } catch (e) {
    payload = null;
  }

  if (!res.ok || (payload && payload.errors)) {
    const err = new Error((payload && payload.message) || 'Ошибка запроса');
    err.errors = {};
    if (payload && payload.errors) {
      Object.keys(payload.errors).forEach((key) => {
        const val = payload.errors[key];
        err.errors[key] = Array.isArray(val) ? val[0] : val;
      });
    }
    throw err;
  }

  if (payload && Object.prototype.hasOwnProperty.call(payload, 'data')) {
    return payload.data;
  }

  return payload;
}

export const notesApi = {
  list: () => request(BASE),
  create: (data) => request(BASE, { method: 'POST', body: JSON.stringify(data) }),
  update: (id, data) => request(BASE + '/' + id, { method: 'PUT', body: JSON.stringify(data) }),
  remove: (id) => request(BASE + '/' + id, { method: 'DELETE' }),
};
