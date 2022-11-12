import {toNearestHundredsLower} from "../utils/helpers";

export default class Item {
    constructor(item = {}) {
        this.id = item.id || '';
        this.barcode = item.barcode || '';
        this.secondary_barcode = item.secondary_barcode || '';
        this.title = item.title || '';
        this.description = item.description || '';
        this.price = 0;
        this.stock_qty = 0;
        this.discount = item.discount || 0;
        this.secondary_discount = 0;
        this.margin = item.margin || 0;
        this.account = item.account || '';
        this.category_id = item.category_id || '';
        this.category = item.category || '';
        this.unit_id = item.unit_id || '';
        this.unit = item.unit || '';
        this.avatar = '';
        this.quantity = 0;
        this.is_secondary = false;
    }

    get grossAmount() {
        return this.quantity * this.price;
    }

    get discountAmount() {
        let amount = this.grossAmount;
        return toNearestHundredsLower(Math.round(amount * this.discount / 100));
    }

    get netAmount() {
        return this.grossAmount - this.discountAmount;
    }
}
