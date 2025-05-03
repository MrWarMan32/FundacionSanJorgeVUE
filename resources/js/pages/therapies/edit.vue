<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem, type SharedData, Therapy } from '@/types'; // Asegúrate de que 'Therapy' esté definido
import { computed, ref, onMounted } from 'vue';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { CircleX } from 'lucide-vue-next';
import Swal from 'sweetalert2';

const props = defineProps({
    therapy: { // Cambiado a therapy, asumiendo que recibes los datos de la terapia a editar
        type: Object as () => Therapy, // Especifica el tipo como Therapy
        required: true,
    },
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Terapias', href: '/therapies' },
    { title: 'Editar Terapia', href: '#' },
];

// Definimos el tipo del formulario
interface TherapyForm {
  name: string;
  description: string;
  duration: string;
}

const form = ref<TherapyForm>({
  name: props.therapy.name ?? '',
  description: props.therapy.description ?? '',
  duration: props.therapy.duration ?? '',
});

const cancel = () => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Deseas cancelar la edición?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, continuar',
    }).then((result) => {
        if (result.isConfirmed) {
            router.visit(route('therapies.index'));
        }
    });
};

const submit = () => {
    if (!form.value.name || !form.value.duration) {
        Swal.fire({
            title: 'Error',
            text: 'Por favor, completa los campos obligatorios.',
            icon: 'error',
            confirmButtonText: 'OK',
        });
        return;
    }

    router.put(route('therapies.update', props.therapy.id), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: '¡Terapia actualizada!',
                text: 'La terapia se actualizó correctamente.',
                icon: 'success',
                confirmButtonText: 'Ir a la lista',
            }).then(() => {
                router.visit(route('therapies.index'));
            });
        },
        onError: (errors) => {
            let errorMessage = 'Hubo un error al intentar actualizar la terapia.';
            if (errors && typeof errors === 'object') {
                for (const key in errors) {
                    const errorValue = errors[key];
                    errorMessage += `\n${key}: ${Array.isArray(errorValue) ? errorValue.join(', ') : errorValue}`;
                }
            }
            Swal.fire({
                title: 'Error',
                html: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK',
            });
        },
    });
};

</script>

<template>
    <Head title="Editar Terapia" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">

            <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
                <CircleX />Cancelar
            </Button>

            <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.8;">

                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Editar Terapia</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span
                                class="text-red-500">*</span></Label>
                        <Input
                            id="name"
                            type="text"
                            v-model="form.name"
                            required
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ingrese el nombre de la terapia"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="description" class="text-sm font-medium text-gray-700 dark:text-gray-300">Descripcion</Label>
                        <Input
                            id="description"
                            type="text"
                            v-model="form.description"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ingrese la descripcion de la terapia"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="duration" class="text-sm font-medium text-gray-700 dark:text-gray-300">Duracion <span
                                class="text-red-500">*</span></Label>
                        <Input
                            id="duration"
                            type="text"
                            v-model="form.duration"
                            required
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Ingrese la duracion de la terapia"
                        />
                    </div>

                </div>

                <div class="mt-6">
                    <Button type="submit" class="bg-green-500 hover:bg-green-600 text-white">
                        Guardar Cambios
                    </Button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>
