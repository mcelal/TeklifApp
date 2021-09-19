<template>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Araç
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fiyat
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">İşlemler</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="car in cars" :key="car.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ car.title }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ car.price }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900" @click="editDialog(car.id)">Düzenle</a> |
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900" @click="delDialog(car.id)">Sil</a>
                                </td>
                            </tr>
                            <tr v-if="!cars.length">
                                <td colspan="3" class="text-red-700 text-center p-6">Henüz kayıt eklenmedi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="dialogEdit">
            <template #title>
                Araç Düzenle
            </template>

            <template #content>
                <div>
                    <form @submit.prevent="updateCar">
                        <div>
                            <jet-label for="title" value="Araç" />
                            <jet-input id="title" type="text" class="mt-1 block w-full" v-model="form.title" required autofocus autocomplete="title" :class="errors.title ? 'bg-red-100' : ''" />
                            <div v-if="errors.title">
                                <ul>
                                    <li v-for="(error, index) in errors.title" :key="index">{{ error }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-4">
                            <jet-label for="price" value="Fiyat" />
                            <jet-input id="price" type="text" class="mt-1 block w-full" v-model="form.price" required autocomplete="price" :class="errors.price ? 'bg-red-100' : ''" />
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
                <jet-button class="ml-4 bg-green-800" :class="{ 'opacity-25': processing }" :disabled="processing" @click="updateCar">
                    Kaydet
                    <Loader v-if="processing" />
                </jet-button>
                <jet-button class="ml-4" @click="dialogEdit = false">
                    İptal
                </jet-button>
            </template>
        </jet-dialog-modal>

        <jet-confirmation-modal :show="dialogDelete">
            <template #title>
                Emin misiniz?
            </template>

            <template #content>
                Bu kaydı silmek istediğinizden emin misiniz?
            </template>

            <template #footer>
                <jet-secondary-button @click="dialogDelete = false">
                    İptal
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click="delCar(this.form.id)" :class="{ 'opacity-25': processing }" :disabled="processing">
                    Sil
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import JetButton from "@/Jetstream/Button.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetInput from "@/Jetstream/Input";
import JetDialogModal from "@/Jetstream/DialogModal.vue";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import Loader from "@/Loader";
import { Inertia } from "@inertiajs/inertia";
import axios from "axios";

export default defineComponent({
    components: {
        JetLabel,
        JetInput,
        JetButton,
        JetDangerButton,
        JetSecondaryButton,
        JetDialogModal,
        JetConfirmationModal,
        Loader
    },
    props: {
        cars: Array,
    },
    data() {
        return {
            dialogEdit: false,
            dialogDelete: false,
            processing: false,
            errors: {},
            form: {
                id: "",
                title: "",
                price: "",
            }
        };
    },
    methods: {
        getCar(id) {
            this.processing = true;
            axios.get(route("cars.edit", id))
                .then(response => {
                    this.form = response.data;
                })
                .catch(error => {
                    alert("Bir sorunla karşılaşıldı");
                })
                .finally(() => {
                    this.processing = false;
                });
        },
        updateCar() {
            this.errors = {};
            this.processing = true;
            axios.put(route("cars.update", this.form.id), this.form)
                .then(response => {
                    if (response.status === 200) {
                        this.dialogEdit = false;
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
        },
        editDialog(id) {
            this.form.id = id;
            this.dialogEdit = true;
            this.getCar(id);
        },
        delDialog(id) {
            this.form.id = id;
            this.dialogDelete = true;
        },
        delCar(id) {
            this.processing = true;
            axios.delete(route("cars.destroy", id))
                .then(response => {
                    if (response.status === 200) {
                        this.dialogDelete = false;
                        Inertia.reload({ only: ["cars"] });
                    }
                })
                .catch(error => {
                    alert("Bir sorunla karşılaşıldı");
                })
                .finally(() => {
                    this.processing = false;
                });
        }
    },
    watch: {
        dialogEdit(val) {
            if (!val) {
                this.form.title = "";
                this.form.price = "";
                this.errors = {};
            }
        },
        dialogDelete(val) {
            if (!val) {
                this.form.id = "";
            }
        }
    }
});
</script>
