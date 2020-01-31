import {toNearestHundredsLower} from "../utils";

export default class Item {
    constructor() {
        this.barcode = '';
        this.title = '';
        this.description = '';
        this.price = 0;
        this.stockQty = 0;
        this.discount = 0;
        this.margin = 0;
        this.type = '';
        this.category = '';
        this.unit = '';
        this.avatar = '';
        this.quantity = 0;
        this.grossAmount = 0;
        this.discountAmount = 0;
        this.netAmount = 0;
    }

    setQuantity(qty) {
        if (this.type === 'buy') {
            this.quantity = qty;
            this.computeAmount();
        } else if (this.type === 'adjust') {
            this.stockQty = qty;
        } else if (this.type === 'sell' && this.stockQty >= qty) {
            this.quantity = qty;
            this.computeAmount();
        }
    }

    setPrice(price) {
        if (price >= 0) {
            this.price = price;
        }
        this.computeAmount();
    }

    setDiscount(rate) {
        if (rate >= 0) {
            this.discount = rate;
        }
        this.computeAmount();
    }

    setStock(qty) {
        this.stockQty = qty;
    }

    increment() {
        if (this.type === 'buy') {
            this.quantity++;
            this.computeAmount();
        } else if (this.type === 'adjust') {
            this.stockQty++;
        } else if (this.stockQty > this.quantity) {
            this.quantity++;
            this.computeAmount();
        } else {
            swal("Maximum limit reached!");
        }
    }

    decrement() {
        if (this.type === 'adjust' && this.stockQty > 0) {
            this.stockQty--;
        } else if (this.quantity > 0) {
            this.quantity--;
            this.computeAmount();
        }
    }

    computeAmount() {
        let amount = this.quantity * this.price;
        this.grossAmount = amount;
        this.discountAmount = toNearestHundredsLower(Math.round(amount * this.discount / 100));
        this.netAmount = this.grossAmount - this.discountAmount;
    }

    /**
     *
     * @param prod {Product}
     * @returns {Item}
     */
    static make(prod){
        let item = new Item();
        item.id = prod.id;
        item.barcode = prod.barcode;
        item.title = prod.title;
        item.category = prod.category;
        item.unit = prod.unit;
        item.price = prod.price;
        item.stockQty = prod.stockQty;
        item.discount = prod.discount;
        item.margin = prod.margin;
        item.quantity = prod.quantity;
        item.avatar = prod.avatar;
        item.type = prod.type;
        return item;
    }
}
