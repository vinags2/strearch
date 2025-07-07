<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, defineAsyncComponent } from 'vue';

// Components
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Button from 'primevue/button'

// const Dialog = defineAsyncComponent(() =>
//   import('primevue/dialog')
// )
  import Dialog from 'primevue/dialog'
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({ password: '', });

const deleteUser = (e: Event) => {
    e.preventDefault();

    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    showAlert.value = false
    form.clearErrors();
    form.reset();
};
const showAlert = ref(false)
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall title="Delete account" description="Delete your account and all of its resources" />
        <div class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
            <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                <p class="font-medium">Warning</p>
                <p class="text-sm">Please proceed with caution, this cannot be undone.</p>
            </div>

            <Button severity="danger" @click="showAlert = true">Delete account</Button>
            <Dialog v-model:visible="showAlert" modal :closable="false" header="Are you sure you want to delete your account?">
                <div>
                    Once your account is deleted, all of its resources and data will also be permanently deleted.
                </div>
                <div class="mt-4">
                    Please enter your password to confirm you would like to permanently delete your account.
                </div>
                <div class="grid gap-2 mt-4">
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" autofocus type="password" name="password" ref="passwordInput" v-model="form.password" placeholder="Password" />
                    <InputError :message="form.errors.password" />
                </div>
                <form class="space-y-6" @submit="deleteUser">
                    <div class="mt-4">
                        <Button severity="info" @click="closeModal"> Cancel </Button>
                        <Button class="ml-4" severity="danger" type="submit" :disabled="form.processing">Delete Account</Button>
                    </div>
                </form>
            </Dialog>
        </div>
    </div>
</template>
