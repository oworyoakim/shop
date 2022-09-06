import {toNearestHundredsLower} from "../utils";

export default class Item {
    constructor() {
        this.barcode = '';
        this.secondaryBarcode = '';
        this.title = '';
        this.description = '';
        this.price = 0;
        this.stockQty = 0;
        this.discount = 0;
        this.secondaryDiscount = 0;
        this.margin = 0;
        this.type = '';
        this.categoryId = '';
        this.category = '';
        this.unitId = '';
        this.unit = '';
        this.avatar = '';
        this.quantity = 0;
        this.isSecondary = false;
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

    /**
     *
     * @param prod {Object}
     * @returns {Item}
     */
    static make(prod) {
        let item = new Item();
        item.id = prod.id;
        item.barcode = prod.barcode;
        item.secondaryBarcode = prod.secondaryBarcode;
        item.title = prod.title;
        item.categoryId = prod.categoryId;
        item.category = prod.category;
        item.unitId = prod.unitId;
        item.unit = prod.unit;
        item.price = prod.price || 0;
        item.stockQty = prod.stockQty || 0;
        item.discount = prod.discount || 0;
        item.secondaryDiscount = prod.secondaryDiscount || 0;
        item.margin = prod.margin || 0;
        item.quantity = prod.quantity || 0;
        item.avatar = prod.avatar || null;
        item.type = prod.type || null;
        return item;
    }
}
