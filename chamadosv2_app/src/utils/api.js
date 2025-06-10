import { Storage } from '@capacitor/storage';

export const api = async (url, method = 'GET', body = null) => {
    const { value: token } = await Storage.get({ key: 'token' });

    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
    };

    const options = {
        method,
        headers,
    };

    if (body) {
        options.body = JSON.stringify(body);
    }

    const response = await fetch(`http://localhost:8000/api/v1/${url}`, options);
    const data = await response.json();

    if (!response.ok) throw new Error(data.message || 'Erro na API');

    return data;
};
