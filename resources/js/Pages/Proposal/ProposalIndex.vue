<template>
    <app-layout title="Teklifler">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Teklifler
                </h2>

                <Link :href="route('proposals.create')">
                    <jet-button>
                        Ekle +
                    </jet-button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <proposals-list :proposals="this.proposals" />
            </div>
        </div>

    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import ProposalsList from "@/Pages/Proposal/Partials/ProposalsList";
import JetButton from "@/Jetstream/Button.vue";
import { Link } from "@inertiajs/inertia-vue3";

export default defineComponent({
    components: {
        AppLayout,
        ProposalsList,
        JetButton,
        Link
    },
    props: {
        proposals: Array,
    },
    data() {
        return {};
    },
    methods: {},
    watch: {}
});
</script>
