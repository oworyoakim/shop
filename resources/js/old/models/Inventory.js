export default class Inventory {
    constructor() {
        this.items = {};
    }

    clearBusket() {
        this.items = {};
    }

    productExists(barcode) {
        return this.items.hasOwnProperty(barcode);
    }

    addItem(item) {
        if (this.productExists(item.barcode)) {
            swal("Product already added to basket!");
            return;
        }
        this.items[item.barcode] = item;
    }

    removeItem(barcode) {
        if (this.productExists(barcode)) {
            delete this.items[barcode];
        }
    }

    updateStock(prodCode, qty) {
        this.items[prodCode].setStock(qty);
    }

    updateItemQuantity(prodCode, qty) {
        this.items[prodCode].setQuantity(qty);
    }

    updatePrice(prodCode, price) {
        this.items[prodCode].setPrice(price);
    }

    updateDiscount(prodCode, rate) {
        this.items[prodCode].setDiscount(rate);
    }
}
