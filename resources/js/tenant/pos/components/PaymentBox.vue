<template>
    <div class="payment-box">
        <div class="form-group">
            <input type="text"
                   placeholder="Ticket barcode"
                   v-model="barcode"
                   @keyup.enter="fetchTicket()"
                   class="form-control form-control-lg"
                   ref="paymentInput"
                   autocomplete="off">
        </div>
        <MainModal v-if="!!ticket" @closed="closeTicketWindow()" :small-text="true">
            <template slot="header">
                <div class="row">
                    <span class="col-4">Ticket ID: {{ ticket.id }}</span>
                    <span class="col-4">Selections: ({{ ticket.betsCount }})</span>
                    <span class="col-4">Cashier: {{ ticket.cashier.name }}</span>
                </div>
            </template>
            <template slot="body">
                <div class="row">
                    <div class="col-sm-4">
                        <ul class="list-unstyled">
                            <li><span class="text-bold">Branch:</span><span
                                class="float-right">{{ ticket.branch.name }}</span>
                            </li>
                            <li><span class="text-bold">Time:</span><span
                                class="float-right">{{ ticket.date }} {{ ticket.time }}</span></li>
                            <li>
                                <span class="text-bold">Status:</span>
                                <span class="badge float-right "
                                      v-bind:class="{'bg-info': ticket.status === 'pending','bg-success': ticket.status === 'won' || ticket.status === 'paid' || ticket.status === 'refund','bg-danger': ticket.status === 'lost','bg-warning': ticket.status === 'canceled'}">{{
                                        ticket.status
                                    }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <ul class="list-unstyled">
                            <li>
                                <span class="text-bold">Odds:</span>
                                <span class="float-right">{{ parseFloat(ticket.ratio).toFixed(2) }}</span>
                            </li>
                            <li>
                                <span class="text-bold">Stake:</span>
                                <span class="float-right">{{ ticket.stake | separator }}</span>
                            </li>
                            <li>
                                <span class="text-bold">Amount:</span>
                                <span v-if="ticket.status === 'pending'"
                                      class="float-right">{{ ticket.amount | separator }}</span>
                                <span v-if="ticket.status !== 'pending'"
                                      class="float-right">{{ ticket.payout | separator }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <ul class="list-unstyled">
                            <li v-if="ticket.bonusAmount > 0">
                                        <span
                                            class="text-bold">Bonus({{
                                                parseFloat(ticket.bonusRate).toFixed(2)
                                            }}%):</span>
                                <span class="float-right">{{ ticket.bonusAmount | separator }}</span>
                            </li>
                            <li v-if="ticket.taxAmount > 0">
                                        <span
                                            class="text-bold">Tax({{ parseFloat(ticket.taxRate).toFixed(2) }}%):</span>
                                <span class="float-right">{{ ticket.taxAmount | separator }}</span>
                            </li>
                            <li v-if="ticket.isWon || ticket.bonusAmount > 0 || ticket.taxAmount > 0">
                                <span class="text-bold">Payout:</span>
                                <span class="text-green float-right">{{ ticket.payout | separator }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-condensed">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Time</th>
                                <th>League</th>
                                <th>Game</th>
                                <th>Market</th>
                                <th>Odd</th>
                                <th>Result</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="bet in ticket.selections"
                                v-bind:class="{'bg-red': bet.status === 'lost','bg-green': bet.status === 'won','bg-yellow': bet.status === 'problematic'}">
                                <td>{{ bet.gameId }}</td>
                                <td>{{ bet.date }} {{ bet.time }}</td>
                                <td>{{ bet.league }}</td>
                                <td>{{ bet.game }}</td>
                                <td>{{ bet.market }}</td>
                                <td class="text-bold">{{ parseFloat(bet.value).toFixed(2) }}</td>
                                <td>
                                    <template v-if="!!bet.ftScore">
                                        {{ bet.ftScore }}({{ bet.htScore }})
                                    </template>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" v-if="ticket.paid || ticket.refunded">
                    <div class="col-sm-12">
                        <h4 class="callout callout-info text-center">Payment completed. (amount:
                            <strong>{{ ticket.paidAmount | separator }}</strong>)
                        </h4>
                    </div>
                </div>
            </template>
            <template slot="footer">
                <div class="row w-100">
                    <div class="col-4">
                        <button type="button" v-if="ticket.isCancelable && isManager" :disabled="isLoading"
                                @click="cancelTicket()" class="btn btn-danger btn-block">Cancel Ticket
                        </button>
                    </div>
                    <div class="col-4">
                        <button v-if="ticket.status === 'won' && isManager"
                                type="button"
                                :disabled="isLoading"
                                @click="approvePayment()" class="btn btn-primary btn-block">Pay Ticket (<b>Amount:
                            {{ ticket.payout | separator }}</b>)
                        </button>
                    </div>
                    <div class="col-4">
                        <button
                            type="button"
                            v-if="ticket.setId === ticket.activeSetId && (ticket.status === 'pending' || ticket.status === 'canceled')"
                            class="btn btn-primary btn-block" @click="addTicketToSlip()">
                            Add To Slip
                        </button>
                    </div>
                </div>
            </template>
        </MainModal>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import MainModal from "@/shared/components/MainModal";
import {EventBus} from "@/utils/httpClient";

export default {
    name: "PaymentBox",
    components: {MainModal},
    mounted() {
        EventBus.$on('loadTicket', this.getUserLastTicket);
    },
    data() {
        return {
            barcode: null,
            isLoading: false,
        };
    },
    computed: {
        ...mapGetters({
            isManager: 'isManager',
            setId: "getActiveSetId",
            ticket: "getTicket",
        }),
    },
    methods: {
        closeTicketWindow() {
            this.barcode = null;
            this.loadTicket();
            this.$refs.paymentInput.focus();
        },

        loadTicket(ticket = null) {
            this.$store.commit('setTicket', ticket);
        },

        addTicketToSlip() {
            let theTicket = this.deepClone(this.ticket);
            this.closeTicketWindow();
            if (this.$route.name === 'bet-window') {
                EventBus.$emit('addToSlip', theTicket);
            } else {
                this.$router.push({name: 'bet-window'});
                this.$nextTick(() => {
                    EventBus.$emit('addToSlip', theTicket);
                });
            }
        },

        async cancelTicket() {
            try {
                let isConfirmed = await this.$store.dispatch("CONFIRM_ACTION",{
                    title: 'Are you sure?',
                    text: "You will cancel this order!",
                    icon: 'warning',
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                });
                if (isConfirmed && !!this.ticket) {
                    let response = await this.$store.dispatch('cancelTicket', this.ticket);
                    await this.$store.dispatch("SET_SNACKBAR",{title: response, icon: 'success'});
                    this.closeTicketWindow();
                    this.getCashiers();
                }
            } catch (error) {
                console.log(error);
                await this.$store.dispatch("SET_SNACKBAR",{title: error, icon: 'error'});
            }
        },

        async fetchTicket() {
            try {
                if (!!this.barcode) {
                    this.isLoading = true;
                    let response = await this.$store.dispatch('fetchTicket', {barcode: this.barcode});
                    this.isLoading = false;
                    this.barcode = null;
                }
            } catch (error) {
                console.log(error);
                await this.$store.dispatch("SET_SNACKBAR",{title: error, icon: 'error'});
                this.isLoading = false;
                this.barcode = null;
                this.$refs.paymentInput.focus();
            }
        },

        async getUserLastTicket(userId) {
            try {
                console.log(userId);
                if (!!userId) {
                    this.isLoading = true;
                    let response = await this.$store.dispatch('getUserLastTicket', {userId});
                    this.isLoading = false;
                    this.barcode = null;
                }
            } catch (error) {
                console.log(error);
                await this.$store.dispatch("SET_SNACKBAR",{title: error, icon: 'error'});
                this.isLoading = false;
                this.barcode = null;
            }
        },

        async approvePayment() {
            try {
                if (!!this.ticket) {
                    this.isLoading = true;
                    let response = await this.$store.dispatch('approvePayment', {barcode: this.ticket.barcode});
                    this.isLoading = false;
                    this.closeTicketWindow();
                    await this.$store.dispatch("SET_SNACKBAR", {title: response});
                }
            } catch (error) {
                console.log(error);
                await this.$store.dispatch("SET_SNACKBAR",{title: error, icon: 'error'});
                this.isLoading = false;
                this.barcode = null;
            }
        },
        getCashiers(){
            return this.$store.dispatch("getCashiers");
        },
    }
}
</script>
