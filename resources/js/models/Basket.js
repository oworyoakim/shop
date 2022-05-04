import moment from "moment";
import {toNearestHundredsUpper} from "../utils";
import Item from "./Item";

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
        this.supplierId = null;
        this.customerId = null;
        this.invoiceNumber = moment.now();
        this.paymentDate = moment().format("YYYY-MM-DD");
        this.paidAmount = 0;
        this.dueDate = '';
    }

    /**
     *
     * @returns {number}
     */
    get count() {
        return Object.keys(this.items).length;
    }

    /**
     * @returns {boolean}
     */
    get isEmpty() {
        return this.count === 0;
    }

    /**
     *
     * @returns {number}
     */
    get balance() {
        return this.tenderedAmount - this.netAmount;
    }

    /**
     *
     * @returns {number}
     */
    get dueAmount() {
        return this.netAmount - this.paidAmount;
    }

    itemExists(barcode) {
        return this.items.hasOwnProperty(barcode);
    }

    /**
     * @param barcode {String}
     * @param data {Object}
     */
    addItem(barcode, data) {
        let item = new Item();
        item.id = data.id;
        item.type = data.type || '';
        item.barcode = data.barcode;
        item.secondaryBarcode = data.secondaryBarcode;
        item.title = data.title;
        item.categoryId = data.categoryId;
        item.category = data.category;
        item.unitId = data.unitId;
        item.unit = data.unit;
        let price = 0;
        if (item.type === 'buy') {
            price = data.costPrice;
        } else if (item.type === 'sell') {
            price = data.sellPrice;
        }
        item.price = price || 0;
        item.stockQty = data.stockQty || 0;
        item.discount = data.discount || 0;
        item.secondaryDiscount = data.secondaryDiscount || 0;
        item.margin = data.margin || 0;
        item.quantity = data.quantity || 0;
        item.avatar = data.avatar || null;
        if (item.type === 'sell' && barcode === item.secondaryBarcode) {
            if (this.itemExists(item.secondaryBarcode)) {
                this.updateItemQuantity(item.secondaryBarcode, 1);
            } else {
                item.isSecondary = true;
                item.discount = item.secondaryDiscount;
                this.items[item.secondaryBarcode] = item;
            }
            this.computeAmount();
        } else if (barcode === item.barcode) {
            if (this.itemExists(item.barcode)) {
                this.updateItemQuantity(item.barcode, 1);
            } else {
                this.items[item.barcode] = item;
            }
            this.computeAmount();
        }
    }

    removeItem(barcode) {
        if (this.itemExists(barcode)) {
            delete this.items[barcode];
            this.computeAmount();
        }
    }

    clear() {
        this.invoiceNumber = moment.now();
        this.items = {};
        this.grossAmount = 0;
        this.netAmount = 0;
        this.taxAmount = 0;
        this.discount = 0;
        this.taxRate = 0;
        this.paidAmount = 0;
        this.supplierId = '';
        this.customerId = '';
        this.dueDate = '';
    }

    setTaxRate(rate) {
        this.taxRate = rate;
    }

    setTenderedAmount(amount) {
        this.tenderedAmount = amount;
    }

    setPaidAmount(amount) {
        if (amount > this.netAmount) {
            return;
        }
        if (amount === this.netAmount) {
            this.dueDate = '';
        }
        this.paidAmount = amount;
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
        this.paidAmount = this.netAmount;
    }

    setItemQuantity(barcode, qty) {
        let item = this.items[barcode];
        if (item instanceof Item) {
            if (item.type === 'buy') {
                item.quantity = qty;
            } else if (item.type === 'adjust') {
                item.stockQty = qty;
            } else if (item.type === 'sell') {
                if (item.stockQty >= qty) {
                    item.quantity = qty;
                } else {
                    swal({
                        title: "Item out of stock!",
                        text: `Only ${item.stockQty} ${item.unit} left.`,
                        icon: 'error'
                    }).then(() => {
                        this.barcode = '';
                        $("#autocompleteInput").focus();
                    });
                }
            }
            this.computeAmount();
        }
    }

    updateItemQuantity(barcode, qty) {
        let item = this.items[barcode];
        if (item instanceof Item) {
            if (item.type === 'buy') {
                item.quantity += qty;
            } else if (item.type === 'adjust') {
                item.stockQty += qty;
            } else {
                let quantity = item.quantity + qty;
                if (item.stockQty >= quantity) {
                    item.quantity = quantity;
                } else {
                    let availableQty = item.stockQty - item.quantity;
                    swal({
                        title: "Item out of stock!",
                        text: `Only ${item.stockQty} ${item.unit} left.`,
                        icon: 'error'
                    }).then(() => {
                        this.barcode = '';
                        $("#autocompleteInput").focus();
                    });
                }
            }
            this.computeAmount();
        }
    }

    setItemPrice(barcode, price) {
        let item = this.items[barcode];
        if (item instanceof Item && price >= 0) {
            item.price = price;
            this.computeAmount();
        }
    }

    setItemDiscount(barcode, rate) {
        let item = this.items[barcode];
        if (item instanceof Item && rate >= 0) {
            item.discount = rate;
            this.computeAmount();
        }
    }

    setItemStock(barcode, qty) {
        let item = this.items[barcode];
        if (item instanceof Item && qty >= 0) {
            item.stockQty = qty;
        }
    }
}
