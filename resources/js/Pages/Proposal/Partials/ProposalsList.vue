<template>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Teklif Kodu
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Müşteri
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    E-Posta
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Toplam Fiyat
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tarih
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">İşlemler</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in proposals" :key="proposals.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ item.id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ item.customer_title }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ item.customer_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ item.total }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ item.created_at }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="item.pdf_link" :href="item.pdf_link" target="_blank" class="text-indigo-600 hover:text-indigo-900">PDF İndir</a> |
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900" @click="delDialog(item.id)">Sil</a>
                                </td>
                            </tr>
                            <tr v-if="!proposals.length">
                                <td colspan="6" class="text-red-700 text-center p-6">Henüz kayıt eklenmedi</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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

                <jet-danger-button class="ml-2" @click="delProposal(this.deletingId)" :class="{ 'opacity-25': processing }" :disabled="processing">
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
        proposals: Array,
    },
    data() {
        return {
            dialogDelete: false,
            deletingId: null,
            processing: false
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
        delDialog(id) {
            this.deletingId = id;
            this.dialogDelete = true;
        },
        delProposal(id) {
            this.processing = true;
            axios.delete(route("proposals.destroy", id))
                .then(response => {
                    if (response.status === 200) {
                        this.dialogDelete = false;
                        Inertia.reload({ only: ["proposals"] });
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
        dialogDelete(val) {
            if (!val) {
                this.deletingId = null;
            }
        }
    }
});
</script>
