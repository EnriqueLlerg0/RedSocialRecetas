<script>
export default {
    name: "VueComponent",
    data() {
        return {
            basicData: {} // Define los datos que deseas pasar al componente dinámico aquí
        };
    },
    mounted() {
        console.log('Componente principal!')
    }
}
</script>

<template>
    <div>
        <p data-bg="naranja">Adios</p>
        <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
                <component :is="Component" :basicData="basicData"></component>
            </transition>
        </router-view>
    </div>
</template>


<style scoped></style>
