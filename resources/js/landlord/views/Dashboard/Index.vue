<template>
    <div class="d-flex flex-column mt-2">
        <template v-if="dashboardStatistics">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6">
                    <DashboardWidget
                        text="Tenants"
                        :stats="dashboardStatistics.length || 0"
                        icon="users"
                    />
                </div>
            </div>
            <div class="row" v-if="$store.getters.HAS_ANY_ACCESS(['admin.tenant.statistics'])">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Statistics</h3>

                            <div class="card-tools">
                                <div class="input-group" style="width: 300px;">
                                    <div class="input-group-prepend">
                                        <button type="button"
                                                class="btn btn-default">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                    </div>
                                    <DateRangePicker
                                        :config="$store.getters.dashboardDateRangePickerConfig"
                                        v-model="activePeriod"
                                        :value="activePeriod"
                                        :wrap="false"
                                        required
                                    />
                                    <div class="input-group-append">
                                        <button type="button"
                                                @click="getDashboardStatistics()"
                                                class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">

                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import DateRangePicker from "@/shared/components/DateRangePicker";
import DashboardWidget from "../../components/DashboardWidget";
import {mapGetters} from "vuex";
import moment from "moment";
export default {
    name: "Dashboard",
    components: {DashboardWidget,DateRangePicker},
    computed: {
        ...mapGetters({
            dashboardStatistics: "DASHBOARD_STATISTICS",
        }),
    },
    mounted() {
        this.getDashboardStatistics();
    },
    data(){
        return {
            activePeriod: moment().format("YYYY-MM-DD") + ' - ' + moment().format("YYYY-MM-DD"),
        }
    },
    methods: {
        getDashboardStatistics(){
            let periods = this.activePeriod.split(' - ');
            let startDate = periods[0];
            let endDate = periods[1];
            return this.$store.dispatch("GET_DASHBOARD_STATISTICS", {startDate, endDate});
        },
    },
}
</script>

<style scoped>

</style>
