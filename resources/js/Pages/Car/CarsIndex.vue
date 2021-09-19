<template>
    <app-layout title="Araçlar">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Araçlar
                </h2>

                <jet-button @click="openDialog">
                    Ekle +
                </jet-button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <cars-list :cars="this.cars" />
            </div>
        </div>

        <jet-dialog-modal :show="dialog">
            <template #title>
                Araç Ekle
            </template>

            <template #content>
                <div>
                    <form @submit.prevent="submit">
                        <div>
                            <jet-label for="car" value="Araç" />
                            <jet-input id="car" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus autocomplete="car" :class="errors.title ? 'bg-red-100' : ''" />
                            <div v-if="errors.title">
                                <ul>
                                    <li v-for="(error, index) in errors.title" :key="index">{{ error }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4">
                            <jet-label for="price" value="Fiyat" />
                            <jet-input id="price" type="text" class="mt-1 block w-full" v-model="form.price" required autocomplete="lastname" :class="errors.price ? 'bg-red-100' : ''" />
                            <div v-if="errors.price">
                                <ul>
                                    <li v-for="(error, index) in errors.price" :key="index">{{ error }}</li>
                                </ul>
                            </div>
                        </div>

                    </form>
                </div>

            </template>

            <template #footer>
                <jet-button
                    class="ml-4 bg-green-800"
                    :class="{ 'opacity-25': processing }"
                    :disabled="processing"
                    @click="submit"
                >
                    Kaydet
                    <Loader v-if="processing" />
                </jet-button>
                <jet-button
                    class="ml-4"
                    @click="dialog = false"
                >
                    İptal
                </jet-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CarsList from "@/Pages/Car/Partials/CarsList";
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
        CarsList,
        JetButton,
        JetDialogModal,
        JetLabel,
        JetInput,
        Loader
    },
    props: {
        cars: Array,
    },
    data() {
        return {
            dialog: false,
            processing: false,
            form: {
                title: "",
                price: "",
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
            axios.post(route("cars.store"), this.form)
                .then(response => {
                    if (response.status === 201) {
                        this.dialog = false;
                        Inertia.reload({ only: ["cars"] });
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
                this.form.title = "";
                this.form.price = "";
                this.errors = {};
            }
        }
    }
});
</script>
