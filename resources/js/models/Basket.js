import moment from "moment";
import {toNearestHundredsUpper} from "../utils";

export default class Basket {
    constructor() {
        this.items = {};
        this.grossAmount = 0;
        this.netAmount = 0;
        this.taxAmount = 0;
        this.discount = 0;
        this.taxRate = 0;
        this.tenderedAmount = 0;
        this.branchId = null;
        this.invoiceNumber = moment().millisecond();
        this.paymentDate = moment().format("YYYY-MM-DD HH:mm:ss");
    }

    /**
     * @returns {boolean}
     */
    get isEmpty() {
        return Object.keys(this.items).length === 0;
    }

    /**
     *
     * @returns {number}
     */
    get count() {
        return Object.keys(this.items).length;
    }


    /**
     *
     * @returns {number}
     */
    get balance() {
        return this.tenderedAmount - this.netAmount;
    }

    itemExists(barcode) {
        return this.items.hasOwnProperty(barcode);
    }

    /**
     *
     * @param item {Item}
     */
    addItem(item) {
        let barcode = item.barcode;
        if (this.itemExists(barcode)) {
            this.items[barcode].increment();
        } else {
            this.items[barcode] = item;
        }
        this.computeAmount();
    }

    removeItem(barcode) {
        if (this.itemExists(barcode)) {
            delete this.items[barcode];
            this.computeAmount();
        }
    }

    clear() {
        this.invoiceNumber = moment().millisecond();
        this.items = {};
        this.grossAmount = 0;
        this.netAmount = 0;
        this.taxAmount = 0;
        this.discount = 0;
        this.taxRate = 0;
    }

    setTaxRate(rate) {
        this.taxRate = rate;
    }

    setTenderedAmount(amount){
        this.tenderedAmount = amount;
    }

    computeAmount() {
        this.grossAmount = 0;
        this.netAmount = 0;
        this.taxAmount = 0;
        this.discount = 0;
        for (let barcode in this.items) {
            if (this.items[barcode].quantity === 0) {
                delete this.items[barcode];
            } else {
                this.grossAmount += this.items[barcode].grossAmount;
                this.netAmount += this.items[barcode].netAmount;
                this.discount += this.items[barcode].discountAmount;
            }
        }
        this.taxAmount = toNearestHundredsUpper(Math.round(this.netAmount * this.taxRate / 100));
        this.netAmount += this.taxAmount;
    }

    incrementItemQuantity(prodCode) {
        this.items[prodCode].increment();
        this.computeAmount();
    }

    decrementItemQuantity(prodCode) {
        this.items[prodCode].decrement();
        this.computeAmount();
    }

    updateItemQuantity(prodCode, qty) {
        this.items[prodCode].setQuantity(qty);
        this.computeAmount();
    }

    updateItemPrice(prodCode, price) {
        this.items[prodCode].setPrice(price);
        this.computeAmount();
    }

    updateItemDiscount(prodCode, rate) {
        this.items[prodCode].setDiscount(rate);
        this.computeAmount();
    }
}
