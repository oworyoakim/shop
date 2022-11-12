import lodash from "lodash";

export function deepClone(object) {
    return lodash.cloneDeep(object);
}

export function prepareQueryParams(payload) {
    let params = [];
    for (let filter in payload) {
        let isValidFilter = payload[filter] !== null &&
            payload[filter] !== undefined &&
            payload[filter] !== 'null' &&
            payload[filter] !== 'undefined' &&
            String(payload[filter]).trim().length > 0;
        if (isValidFilter) {
            params.push(`${filter}=${payload[filter]}`);
        }
    }
    let queryParams = '';
    if (params.length > 0) {
        queryParams = '?' + params.join('&');
    }
    return queryParams;
}

export function resolveError(error) {
    if(!error){return  "";}
    if (!!error.response) {
        return error.response.data;
    }
    if(typeof error === 'string'){
        return  error;
    }
    return error.message ?? error.text;
}

export function isEqual(value, other) {
    return lodash.isEqual(value, other);
}

export function createFormDataFromPayload(payload = {}){
    let formData = new FormData();
    for (let field in payload) {
        if(payload[field]){
            formData.append(field, payload[field]);
        }
    }
    return formData;
}
//to round down to nearest hundreds
export function toNearestHundredsLower(num) {
    return Math.floor(num / 100) * 100;
}
