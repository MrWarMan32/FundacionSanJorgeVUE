<script setup lang="ts">
    import AppLayout from '@/layouts/AppLayout.vue';
    import {type BreadcrumbItem} from '@/types';
    import {ref} from 'vue';
    import {Head, router} from '@inertiajs/vue3';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import {CircleX} from 'lucide-vue-next';
    import Swal from 'sweetalert2';


    const breadcrumbs: BreadcrumbItem[] = [
        {title: 'Terapias', href: '/therapies'},
        {title: 'Agregar Terapia', href: '#'},
    ];

    const form = ref<Partial<{
        name: string;
        description: string;
        duration: string;
    }>>({
        name: '',
        description: '',
        duration: '',
    });


    const resetForm = () => {
        form.value.name = '';
        form.value.description = '';
        form.value.duration = '';
    };

    const cancel = () => {
        Swal.fire({
            title: '¿Cancelar?',
            text: '¿Estás seguro de que deseas cancelar? Se perderán los cambios no guardados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, volver',
        }).then((result) => {
            if (result.isConfirmed) {
                router.visit(route('therapies.index'));
            }
        });
    };


    const submit = () => {
        if (!form.value.name || !form.value.duration) {
            alert('Por favor, completa los campos obligatorios.');
            return;
        }

        router.post(route('therapies.store'), form.value, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire({
                title: '¡Terapia creada!',
                text: 'La terapia se guardó correctamente.',
                icon: 'success',
                confirmButtonText: 'Ir a la lista'
            }).then(() => {
                resetForm();
                router.visit(route('therapies.index'));
            });
            },
            onError: (errors) => {
            let errorMessage = 'Hubo un error al intentar guardar la terapia.';
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
    <Head title="Agregar Terapia" />
    <AppLayout :breadcrumbs="breadcrumbs">

        <div class="p-6 ">

            <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
                <CircleX />Cancelar
            </Button>
        
            <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.8;">

                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Datos de Terapia</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Nombre <span class="text-red-500">*</span></Label>
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
                        <Label for="duration" class="text-sm font-medium text-gray-700 dark:text-gray-300">Duracion <span class="text-red-500">*</span></Label>
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

                <div class="mt-6" >
                    <Button type="submit" class="bg-green-500 hover:bg-green-600 text-white">
                        Guardar
                    </Button>
                </div>

            </form>
        </div>
    </AppLayout>
</template>