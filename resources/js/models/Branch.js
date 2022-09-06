export default class Branch {
    constructor(branch = {}) {
        this.id = branch.id || null;
        this.tenant_id = branch.tenant_id || null;
        this.name = branch.name || null;
        this.phone = branch.phone || null;
        this.email = branch.email || null;
        this.country = branch.country || null;
        this.city = branch.city || null;
        this.balance = branch.balance || null;
        this.address = branch.address || null;
        this.manager = branch.manager || null;
    }
}
