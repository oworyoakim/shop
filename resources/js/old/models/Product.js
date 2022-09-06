export default class Product {
    constructor() {
        this.id = null;
        this.barcode = '';
        this.title = '';
        this.description = '';
        this.price = 0;
        this.stockQty = 0;
        this.discount = 0;
        this.margin = 0;
        this.account = 'both';
        this.type = '';
        this.quantity = 0;
        this.avatar = '';
        this.categoryId = '';
        this.unitId = '';
        this.unit = null;
        this.category = null;
        this.canBeEdited = false;
        this.canBeDeleted = false;
        this.canPrintBarcode = false;
    }
}
