<template>
    <div class="relative">
        <input
            ref="inputSearch"
            tabindex="0"
            v-model="searchParam"
            :placeholder="placeholder"
            :class="inputClass"
            @input="handleInput"
        />
        <div
            v-show="searchParam && showList"
            tabindex="1"
            :class="dropdownClass"
        >
            <ul class="py-1">
                <li
                    v-for="(item, index) in searchResults"
                    :key="index"
                    class="px-3 py-2 cursor-pointer hover:bg-indigo-200"
                    @click="handleSelect(item)"
                >
                    {{ item.title }}
                </li>
                <li
                    v-if="!searchResults.length"
                    class="px-3 py-2 text-center text-red-700"
                >
                    Sonuç Bulunamadı
                </li>
            </ul>
        </div>

    </div>
</template>

<script>
export default {
    name: "AutoComplete",
    props: {
        placeholder: {
            type: String,
            required: false,
            default: "Arama yap..."
        },
        inputClass: {
            type: String,
            required: false,
            default: "w-full border border-blue-300 py-2 px-3 rounded-md focus:outline-none focus:shadow-outline"
        },
        dropdownClass: {
            type: String,
            required: false,
            default: "absolute w-full z-50 bg-gray-100 border border-gray-300 mt-1 mh-48 overflow-hidden overflow-y-scroll rounded-md shadow-md"
        },
        searchUri: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            showList: false,
            selectedItem: "",
            searchParam: "",
            searchResults: [],
            timeout: null
        };
    },
    methods: {
        handleInput(e) {
            if (this.timeout !== null) {
                clearTimeout(this.timeout);
            }

            this.timeout = setTimeout(() => {
                if (this.searchParam.length >= 2) {
                    axios.post(this.searchUri, {
                        query: this.searchParam
                    })
                        .then(response => {
                            this.searchResults = response.data;
                        })
                        .catch(error => {
                            console.log(error);
                        })
                        .finally(() => {
                            this.showList = true;
                        });
                }
            }, 500);
        },
        handleSelect(item) {
            this.searchParam = "";
            this.$emit("selectItem", item);
            this.showList = false;
            this.$refs.inputSearch.focus();
        }
    }
};
</script>

<style scoped>

</style>
