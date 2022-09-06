<template>
    <div class="d-flex flex-column mt-2">
        <Tabs variant="secondary">
            <Tab title="General Settings">
                <GeneralSettings />
            </Tab>
        </Tabs>
    </div>
</template>

<script>
import Tabs from "@/shared/components/Tabs";
import Tab from "@/shared/components/Tab";
import GeneralSettings from "../../components/Settings/GeneralSettings";
export default {
    name: "ManageSettings",
    components: {GeneralSettings, Tabs, Tab},
}
</script>

<style scoped>

</style>
