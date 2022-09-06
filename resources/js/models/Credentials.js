export default class Credentials {
    constructor(credentials = {}) {
        this.loginName = credentials.loginName || '';
        this.currentPassword = credentials.currentPassword || '';
        this.loginPassword = credentials.loginPassword || '';
        this.loginPasswordConfirmation = credentials.loginPasswordConfirmation || '';
    }
}
