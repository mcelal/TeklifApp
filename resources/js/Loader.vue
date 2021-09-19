<template>
    <div class="lds-dual-ring"></div>
</template>

<script>
export default {
    name: "Loader"
};
</script>

<style scoped>
.lds-dual-ring {
    display: inline-block;
    width: 24px;
    height: 24px;
}
.lds-dual-ring:after {
    content: " ";
    display: block;
    width: 20px;
    height: 20px;
    margin: 2px;
    border-radius: 50%;
    border: 2px solid #fff;
    border-color: #fff transparent #fff transparent;
    animation: lds-dual-ring 1.2s linear infinite;
}
@keyframes lds-dual-ring {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
