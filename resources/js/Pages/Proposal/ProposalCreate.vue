<template>
    <app-layout title="Teklif Oluştur">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Teklif Oluştur
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                Müşteri Seçiniz
                <auto-complete
                    :searchUri="route('customers.autocomplete')"
                    @selectItem="selectCustomer"
                />

                <div v-if="form.customer" class="my-2 p-6 bg-white flex items-center space-x-6 rounded-lg shadow-md">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-700 mb-2">{{ form.customer.first_name }} {{ form.customer.last_name }}</h1>
                        <p class="text-gray-600 w-80 text-sm">{{ form.customer.email }}</p>
                    </div>
                </div>

                <div v-if="form.customer" class="my-10">
                    Araç ekleyiniz
                    <auto-complete
                        :searchUri="route('cars.autocomplete')"
                        @selectItem="selectCar"
                    />

                    <div class="flex flex-col">
                        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Marka - Model
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Miktar
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Fiyat
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Toplam
                                                </th>
                                                <th scope="col" class="relative px-6 py-3">
                                                    <span class="sr-only">İşlemler</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr v-for="(car, index) in form.cars" :key="index">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ car.title }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ car.quantity }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ car.price }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ car.total }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="#" class="text-indigo-600 hover:text-indigo-900" @click="removeCar(index)">Sil</a>
                                                </td>
                                            </tr>
                                            <tr v-if="!form.cars.length">
                                                <td colspan="5" class="text-red-700 text-center p-6">Henüz araç eklemediniz</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <jet-button
                        :disabled="!form.cars.length || form.customer === null"
                        class="my-5 w-full bg-green-500 hover:bg-green-800 text-center text-base justify-center"
                        @click="submitForm"
                    >
                        Kaydet
                        <Loader v-if="processing" />
                    </jet-button>
                </div>
            </div>
        </div>

    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CarsList from "@/Pages/Car/Partials/CarsList";
import JetButton from "@/Jetstream/Button.vue";
import Loader from "@/Loader";
import AutoComplete from "@/AutoComplete";
import axios from "axios";

export default defineComponent({
    components: {
        AutoComplete,
        AppLayout,
        CarsList,
        JetButton,
        Loader
    },
    props: {
        cars: Array,
    },
    data() {
        return {
            processing: false,
            form: {
                customer: null,
                cars: []
            },
            errors: {}
        };
    },
    methods: {
        selectCustomer(customer) {
            this.form.customer = customer;
        },
        selectCar(car) {
            let editAction = false;
            this.form.cars.map(item => {
                if (item.id === car.id) {
                    item.quantity++;
                    item.total = item.price * item.quantity;
                    editAction = true;
                }
            });

            if (!editAction) {
                this.form.cars.push({
                    id: car.id,
                    title: car.title,
                    quantity: 1,
                    price: car.price,
                    total: car.price
                });
            }
        },
        removeCar(index) {
            this.form.cars.splice(index, 1);
        },
        submitForm() {
            this.errors = {};
            this.processing = true;
            axios.post(route("proposals.store"), this.form)
                .then(response => {
                    if (response.status === 201) {
                        this.$inertia.visit(route("proposals.index"), { method: "get" });
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        alert("Bir sorunla karşılaşıldı");
                    }
                })
                .finally(() => {
                    this.processing = false;
                });
        }
    }
});
</script>
