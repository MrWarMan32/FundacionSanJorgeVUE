<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Head } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import Swal from 'sweetalert2';
import { Therapy, User, type BreadcrumbItem } from '@/types';
import {CircleX} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {title: 'Terapeutas', href: '/doctor_therapies'},
    {title: 'Asignar Terapia', href: '#'},
];


const props = defineProps({
    doctors: {
        type: Array as () => User[],
    },
    therapies: {
        type: Array as () => Therapy[],
        required: true,
    },
  selectedDoctorId: Number
});

const isDoctorPreselected = !!props.selectedDoctorId;

const form = useForm({
  id_doctor: props.selectedDoctorId ?? '',
  id_therapy: '',
});

function submit() {
    form.post(route('doctor_therapies.store'), {
        onSuccess: () => {
            Swal.fire({
                title: '¡Asignación Exitosa!',
                text: 'La terapia se ha asignado al terapeuta correctamente.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        },
        onError: (error) => {
            Swal.fire({
                title: 'Error',
                text: 'No se pudo asignar la terapia al doctor.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
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
            router.visit(route('doctor_therapies.index'));
        }
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Asignar Terapia" />

        <div class="p-6">

            <Button size="sm" class="bg-red-500 text-white hover:bg-red-700 absolute top-27 right-70 z-10" @click="cancel">
                    <CircleX />Cancelar
            </Button>
            
            <form @submit.prevent="submit" class="max-w-3xl mx-auto space-y-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6" style="opacity: 0.8;">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Asignar terapia</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="space-y-2">
                        <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Terapeuta</Label>
                        <select
                            v-model="form.id_doctor"
                            :disabled="isDoctorPreselected"
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md"
                        >
                            <option value="">Seleccione un doctor</option>
                            <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                            {{ doctor.name }} {{ doctor.last_name }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-sm font-medium text-gray-700 dark:text-gray-300">Terapia</Label>
                        <select 
                            v-model="form.id_therapy" 
                            class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 w-full py-2 rounded-md"
                        >
                            <option disabled value="">Seleccione una terapia</option>
                            <option v-for="therapy in therapies" :key="therapy.id" :value="therapy.id">
                            {{ therapy.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div>
                    <Button type="submit" class="bg-green-500 hover:bg-green-700 text-white right-100">Asignar</Button>
                </div>

            </form>
        </div>
  </AppLayout>
</template>