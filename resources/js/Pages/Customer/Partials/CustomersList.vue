<template>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    İsim
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Soyisim
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    E-Posta
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">İşlemler</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="customer in customers" :key="customer.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ customer.first_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ customer.last_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="text-sm text-gray-900">{{ customer.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900" @click="editDialog(customer.id)">Düzenle</a> |
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900" @click="delDialog(customer.id)">Sil</a>
                                </td>
                            </tr>
                            <tr v-if="!customers.length">
                                <td colspan="4" class="text-red-700 text-center p-6">Henüz kayıt eklenmedi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="dialogEdit">
            <template #title>
                Müşteri Düzenle: {{ form.id }}
            </template>

            <template #content>
                <div>
                    <form @submit.prevent="updateCustomer">
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
                <jet-button
                    class="ml-4 bg-green-800"
                    :class="{ 'opacity-25': processing }"
                    :disabled="processing"
                    @click="updateCustomer"
                >
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
                <jet-secondary-button
                    @click="dialogDelete = false"
                >
                    İptal
                </jet-secondary-button>

                <jet-danger-button
                    class="ml-2"
                    :class="{ 'opacity-25': processing }"
                    :disabled="processing"
                    @click="delCustomer(this.form.id)"
                >
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
        customers: Array,
    },
    data() {
        return {
            dialogEdit: false,
            dialogDelete: false,
            processing: false,
            errors: {},
            form: {
                id: "",
                first_name: "",
                last_name: "",
                email: ""
            }
        };
    },
    methods: {
        getCustomer(id) {
            this.processing = true;
            axios.get(route("customers.edit", id))
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
        updateCustomer() {
            this.errors = {};
            this.processing = true;
            axios.put(route("customers.update", this.form.id), this.form)
                .then(response => {
                    if (response.status === 200) {
                        this.dialogEdit = false;
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
        },
        editDialog(id) {
            this.form.id = id;
            this.dialogEdit = true;
            this.getCustomer(id);
        },
        delDialog(id) {
            this.form.id = id;
            this.dialogDelete = true;
        },
        delCustomer(id) {
            this.processing = true;
            axios.delete(route("customers.destroy", id))
                .then(response => {
                    if (response.status === 200) {
                        this.dialogDelete = false;
                        Inertia.reload({ only: ["customers"] });
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
                this.form.id = "";
                this.form.first_name = "";
                this.form.last_name = "";
                this.form.email = "";
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
