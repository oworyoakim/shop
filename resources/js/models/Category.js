export default class Category {
    constructor(category = {}) {
        this.id = category.id || null;
        this.tenant_id = category.tenant_id || null;
        this.title = category.title || null;
        this.description = category.description || null;
        this.slug = category.slug || null;
        this.items_count = category.items_count || null;
    }
}
