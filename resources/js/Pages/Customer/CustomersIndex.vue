<template>
    <app-layout title="Müşteriler">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Müşteriler
                </h2>

                <jet-button @click="openDialog">
                    Ekle +
                </jet-button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <customers-list :customers="this.customers" />
            </div>
        </div>

        <jet-dialog-modal :show="dialog">
            <template #title>
                Müşteri Ekle
            </template>

            <template #content>
                <div>
                    <form @submit.prevent="submit">
                        <div>
                            <jet-label for="first_name" value="İsim" />
                            <jet-input id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" required autofocus autocomplete="firstname" :class="errors.first_name ? 'bg-red-100' : ''" />
                            <div v-if="errors.first_name">
                                <ul>
                                    <li v-for="(error, index) in errors.first_name" :key="index">{{ error }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4">
                            <jet-label for="last_name" value="Soyisim" />
                            <jet-input id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name" required autocomplete="lastname" :class="errors.last_name ? 'bg-red-100' : ''" />
                            <div v-if="errors.last_name">
                                <ul>
                                    <li v-for="(error, index) in errors.last_name" :key="index">{{ error }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4">
                            <jet-label for="email" value="Email" />
                            <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required :class="errors.email ? 'bg-red-100' : ''" />
                            <div v-if="errors.email">
                                <ul>
                                    <li v-for="(error, index) in errors.email" :key="index">{{ error }}</li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </template>

            <template #footer>
                <jet-button class="ml-4 bg-green-800" :class="{ 'opacity-25': processing }" :disabled="processing" @click="submit">
                    Kaydet
                    <Loader v-if="processing" />
                </jet-button>
                <jet-button class="ml-4" @click="dialog = false">
                    İptal
                </jet-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CustomersList from "@/Pages/Customer/Partials/CustomersList";
import JetButton from "@/Jetstream/Button.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInput from "@/Jetstream/Input";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import Loader from "@/Loader";
import { Inertia } from "@inertiajs/inertia";
import axios from "axios";

export default defineComponent({
    components: {
        AppLayout,
        CustomersList,
        JetButton,
        JetDialogModal,
        JetLabel,
        JetInput,
        Loader
    },
    props: {
        customers: Array,
    },
    data() {
        return {
            dialog: false,
            processing: false,
            form: {
                first_name: "",
                last_name: "",
                email: ""
            },
            errors: {}
        };
    },
    methods: {
        openDialog() {
            this.dialog = !this.dialog;
        },
        submit() {
            this.errors = {};
            this.processing = true;
            axios.post(route("customers.store"), this.form)
                .then(response => {
                    if (response.status === 201) {
                        this.dialog = false;
                        Inertia.reload({ only: ["customers"] });
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    }
                })
                .finally(() => {
                    this.processing = false;
                });
        }
    },
    watch: {
        dialog(val) {
            if (!val) {
                this.form.first_name = "";
                this.form.last_name = "";
                this.form.email = "";
                this.errors = {};
            }
        }
    }
});
</script>
