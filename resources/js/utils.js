export class Errors {
    constructor(data) {
        for (let field in data) {
            this[field] = data[field];
        }
    }

    /**
     *
     * @param field {String}
     * @param value {String}
     */
    set(field, value) {
        this[field] = value;
    }

    /**
     *
     * @param field {string}
     * @returns {null|string}
     */
    get(field) {
        return this[field] || null;
    }

    clear(field = null) {
        if (field) {
            this[field] = null;
            return;
        }
        for (let field in this) {
            this[field] = null;
        }
    }

    has(field) {
        return !!this[field];
    }

    any() {
        return Object.keys(this).length > 0;
    }
}

//to round down to nearest hundreds
export function toNearestHundredsLower(num) {
    return Math.floor(num / 100) * 100;
}

//to round up to nearest hundreds
export function toNearestHundredsUpper(num) {
    return Math.ceil(num / 100) * 100;
}

export function fetchReceivable(url) {
    return fetch(url).then((response) => {
        if (response.ok) {
            return response.json();
        }
        throw Error(response.statusText);
    }).catch((error) => {
        return Promise.reject(error.message);
    });
}

export function fetchItemStock(url) {
    return fetch(url).then((response) => {
        if (response.ok) {
            return response.json();
        }
        throw Error(response.statusText);
    }).catch((error) => {
        return Promise.reject(error.message);
    });
}

export function fetchItems(url) {
    return fetch(url).then((response) => {
        if (response.ok) {
            return response.json();
        }
        throw Error(response.statusText);
    }).catch((error) => {
        return Promise.reject(error.message);
    });
}

export function fetchItemByBarcode(url) {
    return fetch(url).then((response) => {
        if (response.ok) {
            return response.json();
        }
        throw Error(response.statusText);
    }).catch((error) => {
        return Promise.reject(error.message);
    });
}

export function deepClone(object) {
    return JSON.parse(JSON.stringify(object));
}

export function slugify(str) {
    str = String(str).trim();
    return str.split(' ').join('-').toLocaleLowerCase();
}
