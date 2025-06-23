<script setup lang="ts">

const props = defineProps({
    aProp: {
        type: Object,
        required: true,
    },
});

</script>
<template>
    <div>
        {{ aProp.item }}
    </div>
</template>